<?php

namespace Api\Sdk\Connector;

use Api\Sdk\Model\Chapter;
use Api\Sdk\Model\Field;
use Api\Sdk\Query\QueryInterface;
use Api\Sdk\Validator\Constraints\FieldConstraints;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Yaml\Yaml;
use Api\Sdk\Model\Revision;

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
class DataConnector extends AbstractConnector
{
    protected $path;
    protected $validator;

    public function __construct($path = null)
    {
        $path     = $path ? $path : __DIR__ . '/../../../../data';
        $realPath = realpath($path);

        if (false === $realPath) {
            throw new \RuntimeException(sprintf('Folder "%s" does not exist.', $path));
        }

        $this->path      = $realPath;
        $this->validator = Validation::createValidator();
    }

    /**
     * @return array
     */
    public function getCompanies()
    {
        return $this->getDatas("company");
    }

    public function getChapterFields($chapterId)
    {
        $fixtureField = $this->getDatasWithFilters('field', array('chapterId' => $chapterId));

        return $fixtureField;
    }

    public function getAllFieldsFromTree(Chapter $chapter)
    {
        return $this->getChapterFields($chapter->getId());
    }

    /**
     * Return field revision data
     *
     * @param $revisionId revision identifiant
     * @param $fieldId    field identifiant
     *
     * @return array
     */
    public function getFieldValue($revisionId, $fieldId)
    {
        $data = $this->getOneDataWithFilters(
            'revisionFieldValue',
            array('revisionId' => $revisionId, 'fieldId' => $fieldId)
        );

        if (is_array($data) && array_key_exists('value', $data)) {
            return $data['value'];
        }

        return null;

    }

    public function getField($id)
    {
        return $this->getData("field", $id);
    }

    /**
     * Update value of a revision field
     *
     * @param $revisionId revision identifiant
     * @param $data
     *                    format : [
     *                  [fieldId   => value]
     *              ]
     *                    example : [
     *                  [449   => "chaine"],
     *                  [23365 => 6],
     *                  [14598 => 2004],
     *              ]
     *
     * @return bool
     */
    public function updateRevisionFieldValue($revisionId, $data)
    {
        return true;
    }

    /**
     * Validate field value
     *
     * @param $data
     *     format : [
     *                  [fieldId   => value]
     *              ]
     *     example : [
     *                  [449   => "chaine"],
     *                  [23365 => 6],
     *                  [14598 => 2004],
     *              ]
     *
     * @return bool
     */
    public function validateFieldValue($data)
    {
        //validate field data
        foreach ($data as $fieldId => $value) {

            // Check if InvalidArgumentException is found, then no field's data
            try {
                $field = $this->getData("field", $fieldId);
            } catch (\InvalidArgumentException $e) {
                $field = null;
            }

            if ($field) { // Init field's constraints
                $fieldConstraints = new FieldConstraints();

                // Validate field's value
                $violation = $this->validator->validateValue($value, $fieldConstraints->getConstraints($field));

                if ($violation->count() > 0) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Return list of choice for the given list id
     *
     * @param $listId
     *
     * @return array
     */
    public function getListChoices($listId)
    {
        $choicesLists = $this->getDatasWithFilters('choicesList', array('listId' => $listId));

        $choices = array();
        foreach ($choicesLists as $choicesList) {
            $choice                 = $this->getOneDataWithFilters('choice', array('id' => $choicesList['choiceId']));
            $choices[$choice['id']] = $choice['name'];
        }

        return $choices;
    }

    /**
     * @param $type
     * @param $id
     *
     * @return array|bool|float|int|mixed|null|number|string
     * @throws \InvalidArgumentException
     */
    private function getData($type, $id)
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
    private function getDatasWithFilters($type, $filters)
    {
        $datas   = array();
        $dirPath = $this->path . '/' . $type;

        if ($directory = opendir($dirPath)) {
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
    private function getOneDataWithFilters($type, $filters)
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
    private function getDatas($type, QueryInterface $query = null)
    {
        $datas   = array();
        $dirPath = $this->path . '/' . $type;

        if ($directory = opendir($dirPath)) {

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

    private function matchFilters($data, $filters)
    {
        if (count($filters) > 0) {
            foreach ($filters as $field => $value) {
                if (!isset($data[$field]) || $data[$field] != $value) {
                    return false;
                }
            }
        }

        return true;
    }

    private function convertQuery(QueryInterface $query = null)
    {
        $filters = array();
        $offset  = 0;
        $limit   = null;

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

    private function buildQueryFilters($query)
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


    /**
     * Get all field's values of Revision
     *
     * @param Revision $revision
     *
     * @return array|bool|float|int|mixed|null|number|string
     */
    public function getValues(Revision $revision)
    {
        $values = $this->getData('revisionFieldsValues', $revision->getId());

        return $values;
    }

}
