<?php

namespace Api\Sdk\Document\Connector\Doctrine;

use Doctrine\ORM\EntityManager;

use Api\Sdk\Connector\AbstractDoctrineConnector;
use Api\Sdk\Document\Connector\DocumentConnectorInterface;
use Api\Sdk\Document\Query\DocumentQuery;
use Api\Sdk\Model\BaseModel;
use Api\Sdk\Query\SortQuery;
use Api\Sdk\Model\Contract;
use Api\Sdk\Model\Document;
use Api\SdkBundle\Entity\Document as DocumentEntity;
use Api\Sdk\Query\QueryInterface;

/**
 * This class allows to use sf2 document entity model
 *
 * Class DocumentDoctrineConnector
 */
class DocumentDoctrineConnector extends AbstractDoctrineConnector implements DocumentConnectorInterface
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em);

        $this->setRepository('Document');
    }

    /**
     * Return the document matching the given id
     *
     * @param int $id
     *
     * @return array|null
     */
    public function getById($id)
    {
        return $this->getOne($id);
    }

    /**
     * Returns documents data thanks to identifiants
     *
     * @param array Documents identifiants
     *
     * @return array documents data
     */
    public function getByIds($ids)
    {
        if(empty($ids)) {
            return [];
        }

        $query = new DocumentQuery(['ids' => $ids]);

        return $this->getCollection($query);
    }

    /**
     * Adds the given document to a relation, i.e. either a Contract or a Revision
     *
     * @param Document  $document
     * @param BaseModel $relation
     *
     * @todo split this method on addDocumentToContract and addDocumentToRevision
     * @return array
     */
    public function addDocumentToRelation(Document $document, BaseModel $relation)
    {
        $this->em->clear();

        $documentEntity = $this->repository->find($document->getId());
        $colleagueName  = $relation instanceof Contract ? 'contractDoctrine' : 'revisionDoctrine';
        $relationEntity = $this->getMediator()->getColleague($colleagueName)->repository->find($relation->getId());

        $relationEntity->addDocument($documentEntity);

        $this->em->persist($relationEntity);
        $this->em->flush();

        return $this->convert($relationEntity);
    }

    /**
     * Creates a new document from the given data
     *
     * @param array $data
     *
     * @return array
     */
    public function createDocument(array $data)
    {
        $documentEntity = new DocumentEntity();
        $documentEntity = $this->populateDocument($documentEntity, $data);
        $documentEntity->setId(null);

        $this->em->persist($documentEntity);
        $this->em->flush($documentEntity);

        return $this->convert($documentEntity);
    }

    /**
     * Update the document matching $data['id'] with the given data
     *
     * @param array $data
     *
     * @return array
     */
    public function updateDocument(array $data)
    {
        $documentEntity = $this->repository->find($data['id']);
        $documentEntity = $this->populateDocument($documentEntity, $data);

        $this->em->persist($documentEntity);
        $this->em->flush($documentEntity);

        return $this->convert($documentEntity);
    }

    /**
     * Populate document's data
     * With only relation DocumentType, not contract and revision
     *
     * @param DocumentEntity $documentEntity
     * @param array          $data
     *
     * @return DocumentEntity
     */
    private function populateDocument(DocumentEntity $documentEntity, array $data)
    {
        if (isset($data['type']) && !empty($data['type'])) {
            $data['type'] = $this->getMediator()->getColleague('documentTypeDoctrine')->repository->find($data['type']);
        }

        /**
         * Special case for non-required input
         */
        if (!isset($data['reference']) || empty($data['reference'])) {
            $documentEntity->setReference('');
        }
        if (!isset($data['description']) || empty($data['description'])) {
            $documentEntity->setDescription('');
        }
        if (!isset($data['releasedAt']) || empty($data['releasedAt'])) {
            $documentEntity->setReleasedAt(null);
        }

        $documentEntity->populate($data);

        return $documentEntity;
    }

    /**
     * Gets the documents matching the given query
     *
     * @param QueryInterface $query
     * @param bool           $convert if true, convert into an array
     *
     * @return array|Document
     */
    public function getCollection(QueryInterface $query, $convert = true)
    {
        $documentsEntity = $this->getResult($query);

        if (!$convert) {
            return $documentsEntity;
        }

        return array_map(function ($documentEntity) {
            return $this->convert($documentEntity);
        }, $documentsEntity);
    }

    /**
     * Returns the number of documents matching the given query
     *
     * @param QueryInterface $query
     *
     * @return int
     */
    public function count(QueryInterface $query)
    {
        return $this->getCount($query);
    }

    /**
     * Returns wether the given document belongs to at least a revision, or not
     *
     * @param int $documentId
     *
     * @return boolean
     */
    public function belongsToRevisions($documentId)
    {
        $revisionRepository      = 'revision';
        $alias                   = lcfirst(substr($revisionRepository, 0, 1));
        $revisionRepository      = sprintf('ApiSdkBundle:%s', $revisionRepository);
        $revisionRepository      = $this->em->getRepository($revisionRepository);
        $qb                      = $revisionRepository->createQueryBuilder($alias);

        $documentIdSelector = sprintf('%s.id', 'd');
        $documentEqual      = $this->qb->expr()->eq($documentIdSelector, $documentId);

        $qb
            ->leftJoin('r.documents', 'd')
            ->where($documentEqual)
        ;

        return count($qb->getQuery()->getResult()) > 0;
    }

    /**
     * Deletes the document mathcing the given id
     *
     * @param int $id
     *
     * @return bool
     *
     */
    public function deleteDocument($id)
    {
        $documentEntity = $this->repository->find($id);

        $this->em->remove($documentEntity);
        $this->em->flush();

        return true;
    }

    /**
     * Returns all the revisions containing the document matching the given id
     *
     * @param $id document id
     *
     * @return array revisions data
     *
     */
    public function getRevisionsDocument($id)
    {
        $revisions = array();
        $document = $this->repository->find($id);

        foreach ($document->getRevisions() as $revision) {
            $revisions[] = $revision->toArray();
        }

        return $revisions;
    }

    /**
     * Returns the collection of documents for the given contract
     *
     * @param Contract $contract
     *
     * @return array array of Api\Sdk\Model\Document
     */
    public function getContractDocuments(Contract $contract)
    {
        $query = new SortQuery(new DocumentQuery(array('contract' => $contract)), [['releasedAt', 'DESC']]);

        return $this->getDocuments($query);
    }

    /**
     * Returns true if a document with the specified file path already exists, false otherwise
     *
     * @param $filePath
     * @return mixed
     */
    public function alreadyExists($filePath)
    {
        return ($this->repository->findOneBy(['filePath' => $filePath])) ? true : false;
    }
}
