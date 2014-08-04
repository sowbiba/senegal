<?php

namespace Api\Sdk\Document\Connector\Doctrine;

use Doctrine\ORM\EntityManager;
use Api\Sdk\Connector\AbstractDoctrineConnector;

/**
 * This class allows to use sf2 DocumentType entity model
 *
 * Class DocumentTypeDoctrineConnector
 * @package Api\Sdk\Connector\DoctrineConnector
 * @author Florent Coquel
 *
 * Can't test it without a context (database)
 * @codeCoverageIgnore
 */
class DocumentTypeDoctrineConnector extends AbstractDoctrineConnector
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em);

        $this->setRepository('DocumentType');
    }

    /**
     * Returns DocumentType's data
     *
     * @param int $id
     *
     * @return array|null
     */

    public function getDocumentType($id)
    {
        return $this->getOne($id);
    }

    /**
     * @return mixed
     */
    public function getDocumentTypes()
    {
        return $this->repository->createQueryBuilder($this->repositoryAlias)->getQuery()->getArrayResult();
    }
}
