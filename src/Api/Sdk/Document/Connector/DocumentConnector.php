<?php
/**
 * Author: Florent Coquel
 * Date: 30/10/13
 */

namespace Api\Sdk\Document\Connector;

use Api\Sdk\Connector\AbstractConnector;

use Api\Sdk\Model\Contract;
use Api\Sdk\Model\Document;
use Api\Sdk\Model\Revision;

class DocumentConnector extends AbstractConnector implements DocumentConnectorInterface
{
    /**
     * Returns documents data thanks to identifiants
     *
     * @param array Documents identifiants
     *
     * @return array documents data
     */
    public function getByIds($ids)
    {
        return $this->getConnectorToUse("getByIds")->getByIds($ids);
    }

    /**
     * @inheritdoc
     *
     * @param Document          $document
     * @param Contract|Revision $relation
     *
     * @return array
     */
    public function addDocumentToRelation(Document $document, $relation)
    {
        return $this->getConnectorToUse("addDocumentToRelation")->addDocumentToRelation($document, $relation);
    }

    /**
     * @inheritdoc
     *
     * @param int $documentId
     *
     * @return boolean
     */
    public function belongsToRevisions($documentId)
    {
        return $this->getConnectorToUse("belongsToRevisions")->belongsToRevisions($documentId);
    }

    /**
     * @inheritdoc
     *
     * @param array $data
     *
     * @return array
     */
    public function createDocument(array $data)
    {
        return $this->getConnectorToUse("createDocument")->createDocument($data);
    }

    /**
     * @inheritdoc
     *
     * @param int $documentId
     *
     * @return bool
     *
     */
    public function deleteDocument($documentId)
    {
        return $this->getConnectorToUse("deleteDocument")->deleteDocument($documentId);
    }

    /**
     * @inheritdoc
     *
     * @param Contract $contract
     *
     * @return array array of Api\Sdk\Model\Document
     */
    public function getContractDocuments(Contract $contract)
    {
        return $this->getConnectorToUse("getContractDocuments")->getContractDocuments($contract);
    }

    /**
     * @inheritdoc
     *
     * @param int $documentId
     *
     * @return array revisions data
     *
     */
    public function getRevisionsDocument($documentId)
    {
        return $this->getConnectorToUse("getRevisionsDocument")->getRevisionsDocument($documentId);
    }

    /**
     * @inheritdoc
     *
     * @param array $data
     *
     * @return array
     */
    public function updateDocument(array $data)
    {
        return $this->getConnectorToUse("updateDocument")->updateDocument($data);
    }

    public function getUser($userId)
    {
        return $this->getMediator()->getColleague("user")->getById($userId);
    }

    public function getDocumentType($documentTypeId)
    {
        return $this->getConnectorToUse("getDocumentType")->getDocumentType($documentTypeId);
    }

    public function getDocumentTypes()
    {
        return $this->getConnectorToUse("getDocumentType")->getDocumentTypes();
    }

    /**
     * @inheritdoc
     *
     * @param $filePath
     * @return mixed
     */
    public function alreadyExists($filePath)
    {
        return $this->getConnectorToUse("alreadyExists")->alreadyExists($filePath);
    }
}
