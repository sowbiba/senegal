<?php

namespace Senegal\ApiBundle\Manager;

use Doctrine\ORM\EntityManager;

/**
 * @codeCoverageIgnore
 */
abstract class AbstractManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $entityClass;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $repository;

    /**
     * @param EntityManager $em
     * @param               $entityClass
     */
    public function __construct(EntityManager $em, $entityClass)
    {
        $this->em          = $em;
        $this->entityClass = $entityClass;
        $this->repository  = $this->em->getRepository($this->entityClass);
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->em;
    }

    final public function find($id)
    {
        return $this->repository->find($id);
    }

    final public function findAll()
    {
        return $this->repository->findAll();
    }

    final public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    final public function findOneBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }
}
