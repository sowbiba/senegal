<?php
namespace Api\Sdk\Document;

use Api\Sdk\AbstractSdk;
use Api\Sdk\Document\Query\DocumentQuery;
use Api\Sdk\Model\BaseModel;
use Api\Sdk\Model\Contract;
use Api\Sdk\Model\Document;
use Api\Sdk\Model\DocumentType;
use Api\Sdk\Model\Revision;

use Api\Sdk\Model\User;
use Api\Sdk\Query\QueryInterface;
use Api\Sdk\QueryableSdkInterface;

/**
 * This class lists all the high-level methods for Document objects.
 *
 * This class can only use POPO classes (\Api\Sdk\Model)
 * To use this class you have to initialize a connector (\Api\Sdk\Connector) and pass it to the constructor
 * These connectors work with POPO objects, to save an object you have to pass it
 * Only connectors can use entities (\Api\SdkBundle\Entity)
 *
 *
 * Class DocumentSdk
 * @package Api\Sdk\Document
 * @author Florent Coquel
 * @since 17/09/13
 */
class DocumentSdk extends AbstractSdk implements QueryableSdkInterface
{
    /**
     * Returns the document matching the given id
     *
     * @param int $id
     *
     * @return null|Document
     * @throws \BadMethodCallException
     */
    public function getById($id)
    {
        if (!is_int($id) || $id == 0) {
            throw new \BadMethodCallException(__METHOD__ . "(): Wrong parameter, document id must be an integer and valid !!!");
        }

        $data = $this->connector->getById($id);

        return empty($data) ? null : new Document($this, $data);
    }

    /**
     * Returns documents thanks to identifiants
     *
     * @param array Documents identifiants
     *
     * @return array \Api\Sdk\Model\Document[]
     */
    public function getByIds($ids)
    {
        $documents = $this->connector->getByIds($ids);

        return array_map(function($document) { return new Document($this, $document); }, $documents);
    }
    
    public function getDocumentTypes()
    {
        return $this->connector->getDocumentTypes();
    }
            

    /**
     * Returns the DocumentType matching the given id
     *
     * @param Document $document
     *
     * @return null|DocumentType
     * @throws \BadMethodCallException
     */
    public function getType($id)
    {
        if (!is_int($id) || $id == 0) {
            throw new \BadMethodCallException(__METHOD__ . "(): Wrong parameter, document id must be an integer and valid !!!");
        }
        $data = $this->connector->getDocumentType($id);

        return empty($data) ? null : new DocumentType($this, $data);
    }

    /**
     * Creates a new Document from the given data
     *
     * @param Document $document
     *
     * @return mixed
     *
     * Need a database
     * @see test on document connector
     * @codeCoverageIgnore
     */
    protected function doCreate($document)
    {
        $result = $this->connector->createDocument($document->toArray());

        $document->setId($result['id']);

        return $result;
    }

    /**
     * Links the given document and contract, i.e. add the document to the contract
     *
     * If contract is child, call method with the parent
     * If contract is parent, add documents to its child which herits documents
     *
     * @todo may be this method needs to be in ContractSdk
     * @todo the usage of addDocumentToRelation is not very pretty => refactor?
     *
     * @param Document $document The document
     * @param Contract $contract The contract
     *
     * Need a database
     * @see test on document connector
     * @codeCoverageIgnore
     */
    public function addDocumentToContract(Document $document, Contract $contract)
    {
        $this->connector->addDocumentToRelation($document, $contract);

        if ($contract->isParent()) {
            foreach ($contract->getChildren() as $childContract) {
                if ($childContract->inheritsDocuments()) {
                    $this->connector->addDocumentToRelation($document, $childContract);
                }
            }
        }
    }

    /**
     * Updates the given document with data
     *
     * @see    createRevision For more information about $data's parameters
     * @param  array         $data
     * @return null|Document
     *
     * Need a database
     * @see test on document connector
     * @codeCoverageIgnore
     */
    protected function doUpdate($document)
    {
        return $this->connector->updateDocument($document->toArray());
    }

    /**
     * Links the given document to a revision, i.e. add the document to the revision
     *
     * If the contrat of the revision inherits documents then add the document to the parent contract
     *
     * @todo may be this method needs to be in RevisionSdk
     * @todo the usage of addDocumentToRelation is not very pretty => refactor?
     *
     * @param Document $document The document
     * @param Revision $revision The revision
     *
     * @return Document
     *
     * Need a database
     * @codeCoverageIgnore
     */
    public function addDocumentToRevision(Document $document, Revision $revision)
    {
        $this->connector->addDocumentToRelation($document, $revision);

        $contract = $revision->getContract();

        if ($contract->inheritsDocuments()) {
            $this->addDocumentToContract($document, $contract->getParent());
        }

        return $document;
    }

    /**
     * Returns wether the given document belongs to at least one revision, or not
     * @todo : may be this method is the same as getRevisions() with a count ...
     *
     * @param int $documentId
     *
     * @return boolean
     *
     * Need a database
     * @codeCoverageIgnore
     */
    public function belongsToRevisions($documentId)
    {
        return $this->connector->belongsToRevisions($documentId);
    }

    /**
     * Removes a Document, in database and physically
     *
     * @param int $id
     *
     * @return bool
     *
     * Need a database
     * @codeCoverageIgnore
     */
    protected function doDelete(BaseModel $object)
    {
        if (!$object->canBeDeleted()) {
            return false;
        }

        return $this->connector->deleteDocument($object->getId());

    }

    /**
     * Returns the documents collection matching the given query
     *
     * For a list of document's revision, you must pass a DocumentQuery with a filter on the revision
     *
     * @param  QueryInterface $query (DocumentQuery)
     * @return array          array of Api\Sdk\Model\Document
     **/
    public function getCollection(QueryInterface $query)
    {
        $datas = $this->connector->getCollection($query);

        $documents = array_map(function ($data) {
            return new Document($this, $data);
        }, $datas);

        return $documents;
    }

    /**c
     * Returns the number of Document matching the given query
     *
     * @param QueryInterface $query
     *
     * @return mixed
     */
    public function count(QueryInterface $query)
    {
        return $this->connector->count($query);
    }

    /**
     * Returns all the revisions the given document belongs to
     *
     * @param $document Document
     *
     * @return array array of Api\Sdk\Model\Revision
     */
    public function getRevisions(Document $document)
    {
        $datas = $this->connector->getRevisionsDocument($document->getId());

        return array_map(function ($data) {
            return new Revision($this->getMediator()->getColleague("revision"), $data);
        }, $datas);
    }

    /**
     * Returns the user matching the given id
     *
     * @todo this method has nothing to do here. As it is, it should be in UserSdk. Maybe we should have a getCreatedBy() and getUpdatedBy() methods instead
     *
     * @param  int                     $userId
     * @return null|User
     * @throws \BadMethodCallException
     */
    public function getUser($userId)
    {
        if (is_null($userId)) {
            return null;
        }

        if (!is_int($userId)) {
            throw new \BadMethodCallException(__METHOD__ . "(): Wrong parameter, user id must be an integer !!!");
        }

        $userData = $this->connector->getUser($userId);

        return empty($userData) ? null : new User($this->getMediator()->getColleague("user"), $userData);
    }

    /**
     * @param string $classname
     *
     * @return bool
     */
    public function supports($classname)
    {
        return $classname === 'Api\Sdk\Model\Document';
    }

    /**
     * Returns true if a document with the specified file path already exists, false otherwise
     *
     * @param $filePath
     * @return mixed
     */
    public function alreadyExists($filePath)
    {
        return $this->connector->alreadyExists($filePath);
    }

    /**
     * @param array $filters
     *
     * @return \Api\Sdk\Query\QueryInterface
     */
    public function getQuery(array $filters = array())
    {
        return new DocumentQuery($filters);
    }
}
