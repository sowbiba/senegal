<?php

namespace Api\Sdk\Document\Connector\Data;

use Api\Sdk\Connector\AbstractDataConnector;

use Api\Sdk\Model\Contract;
use Api\Sdk\Model\Document;
use Api\Sdk\Query\QueryInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * This is use in order to retrieve fake data
 *
 * Class DataConnector
 * @package Api\Sdk\Connector
 * @author  Florent Coquel
 * @since   14/06/13
 *
 * @SuppressWarnings(PHPMD)
 *
 */
class DocumentDataConnector extends AbstractDataConnector
{
    /**
     * @see DocumentDoctrineConnector::getByIds
     */
    public function getByIds(array $ids)
    {
        return $this->getDatasWithFilters('document', ['id' => $ids]);
    }

    /**
     * @param $id document identifiant
     *
     * @return array
     */
    public function getRevisionsDocument($id)
    {
        $document = $this->getData('document', $id);

        return $this->getDatasWithFilters('revision', array('Id' => $document['revisionId']));
    }

    public function getContractDocuments(Contract $contract)
    {
        return $this->getDatasWithFilters('document', array('contractId' => $contract->getId()));
    }

    /**
     * @param QueryInterface $query
     * @param bool           $convert
     *
     * @return array
     */
    public function getCollection(QueryInterface $query)
    {
        return $this->getDatas("document", $query);
    }

    /**
     * @param QueryInterface $query
     *
     * @return int
     */
    public function count(QueryInterface $query)
    {
        return count($this->getDatas("document", $query));
    }

    /**
     * @param int $id
     *
     * @return array|bool|float|int|mixed|null|number|string
     */
    public function getById($id)
    {
        return $this->getData("document", $id);
    }

    /**
     * @param $id
     *
     * @return array
     */
    public function getDocumentType($id)
    {
        return $this->getData("documentType", $id);
    }

    /**
     * @param $filePath
     * @return bool
     */
    public function alreadyExists($filePath)
    {
        $dirPath = $this->path . '/' . 'document';

        if ($directory = opendir($dirPath)) {
            while (false !== ($file = readdir($directory))) {
                if ($file != "." && $file != "..") {
                    $data = Yaml::parse($dirPath . '/' . $file);

                    if (isset($data['filePath']) && $data['filePath'] === $filePath) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

}
