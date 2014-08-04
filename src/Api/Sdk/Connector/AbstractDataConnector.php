<?php

namespace Api\Sdk\Connector;

use Api\Sdk\Model\Field;
use Api\Sdk\Query\QueryInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Yaml\Yaml;

/**
 * This is use in order to retrieve fake data
 *
 * Class DataConnector
 * @package Api\Sdk\Connector
 * @author Florent Coquel
 * @since 14/06/13
 *
 * @SuppressWarnings(PHPMD)
 *
 */
class AbstractDataConnector extends AbstractConnector
{
    protected $path;
    protected $validator;

    public function __construct($path = null)
    {
        $path = $path ? $path : __DIR__ . '/../data';
        $realPath = realpath($path);

        if (false === $realPath) {
            throw new \RuntimeException(sprintf('Folder "%s" does not exist.', $path));
        }

        $this->path = $realPath;
        $this->validator = Validation::createValidator();
    }

    /**
     * @param $type
     * @param $id
     *
     * @return array|bool|float|int|mixed|null|number|string
     * @throws \InvalidArgumentException
     */
    protected function getData($type, $id)
    {
        if (is_null($id)) {
            return null;
        }

        $file = sprintf('%s/%s/%s.yml', $this->path, $type, $id);

        if (!file_exists($file)) {
            throw new \InvalidArgumentException(sprintf('Unable to find data for %s#%s (expected file: %s).', $type, $id, $file));
        }

        return Yaml::parse($file);
    }

    /**
     * This method return all data which match specific filters
     *
     * @param $type
     * @param $filters
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function getDatasWithFilters($type, $filters)
    {
        $datas = array();
        $dirPath = $this->path . '/' . $type;

        if ($directory = @opendir($dirPath)) {
            while (false !== ($file = readdir($directory))) {
                if ($file != "." && $file != "..") {
                    $data = Yaml::parse($dirPath . '/' . $file);

                    if ($this->matchFilters($data, $filters)) {
                        $datas[] = $data;
                    }
                }
            }
            closedir($directory);
        }

        return $datas;
    }

    /**
     * @param $type
     * @param $filters
     *
     * @return array|null
     * @throws \InvalidArgumentException
     */
    protected function getOneDataWithFilters($type, $filters)
    {
        $datas = $this->getDatasWithFilters($type, $filters);

        return array_shift($datas);
    }

    /**
     * This method return all data which match query
     *
     * @param                $type
     * @param QueryInterface $query
     *
     * @return array
     */
    protected function getDatas($type, QueryInterface $query = null)
    {
        $datas = array();
        $dirPath = $this->path . '/' . $type;

        if ($directory = @opendir($dirPath)) {

            list($filters, $offset, $limit) = $this->convertQuery($query);

            while (false !== ($file = readdir($directory))) {
                if ($file != "." && $file != "..") {
                    $data = Yaml::parse($dirPath . '/' . $file);

                    if ($this->matchFilters($data, $filters)) {
                        $datas[] = $data;
                    }
                }
            }
            closedir($directory);

            $datas = array_slice($datas, $offset, $limit, true);

        }

        return $datas;
    }

    protected function matchFilters($data, $filters)
    {
        if (count($filters) > 0) {
            foreach ($filters as $field => $value) {
                if (!isset($data[$field])) {

                    return false;
                }
                if (is_array($value) && !in_array($data[$field], $value)) {

                    return false;
                } elseif (!is_array($value) && $data[$field] != $value) {

                    return false;
                }
            }
        }

        return true;
    }

    protected function convertQuery(QueryInterface $query = null)
    {
        $filters = array();
        $offset = 0;
        $limit = null;

        if (!is_null($query)) {
            $query = $query->toArray();

            // has a sub query
            while (isset($query["query"])) {
                if (isset($query['limit'])) {
                    $limit = $query['limit'];
                }
                if (isset($query['offset'])) {
                    $offset = $query['$offset'];
                }
                $query = $query['query'];
            }
            // this is final query
            unset($query['_type']);
            $filters = $this->buildQueryFilters($query);
        }

        return array($filters, $offset, $limit);
    }

    protected function buildQueryFilters($query)
    {
        $filters = array();
        foreach ($query as $property => $value) {
            if (is_object($value)) {
                if (method_exists($value, "getId")) {
                    $filters[$property . "Id"] = $value->getId();
                }
            } elseif (is_null($value)) {
                continue;
            } else {
                $filters[$property] = $value;
            }
        }

        return $filters;
    }
}
