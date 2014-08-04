<?php
namespace Api\Sdk\Connector;

use Doctrine\ORM\EntityManager;

use Doctrine\ORM\EntityRepository;
use Api\Sdk\Query\QueryInterface;

/**
 * This class allow to use sf2 entity model
 *
 * Class DoctrineConnector
 * @package Api\Sdk\Connector\DoctrineConnector
 * @author Florent Coquel
 * @since 19/06/13
 *
 * Can't test it without a context (database)
 * @codeCoverageIgnore
 *
 */
abstract class AbstractDoctrineConnector extends AbstractConnector
{
    public $em;
    public $qb;

    /**
     * @var EntityRepository
     */
    public $repository;

    public $repositoryAlias;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $repository
     */
    public function setRepository($repository)
    {
        $this->repositoryAlias = lcfirst(substr($repository, 0, 1));
        $repository            = sprintf('ApiSdkBundle:%s', $repository);
        $this->repository      = $this->em->getRepository($repository);

        $this->setQb();
    }

    /**
     *
     */
    public function setQb()
    {
        $this->qb = $this->repository->createQueryBuilder($this->repositoryAlias);
    }

    /**
     * @param $object
     *
     * @return array
     */
    public function convert($object)
    {
        //@todo transform to array recursively !!!
        return !is_array($object) ? $object->toArray(false) : $object;
    }

    /**
     * Return one result
     *
     * @param int|\Api\Sdk\Query\QueryInterface $query instance of \Api\Sdk\Query\QueryInterface or identifiant
     *
     * @return null|\Api\SdkBundle\Entity\BaseEntity
     */
    public function getOne($query)
    {
        if($query instanceof QueryInterface) {
            $qb     = $query->matchDoctrine($this->qb, null);
            $result = $qb->getQuery()->getOneOrNullResult();

            $this->setQb();

            return $result;
        }

        $object = $this->repository->find($query);

        $this->setQb();

        if (!$object) {
            return null;
        }

        if (method_exists($object, 'postLoadHydrate')) {
            $object->postLoadHydrate();
        }

        return $this->convert($object);
    }

    /**
     * @param QueryInterface $query
     *
     * @return mixed
     */
    public function getCount(QueryInterface $query)
    {
        $qb     = $query->matchDoctrine($this->qb, null);
        $result = $qb
            ->select(sprintf('COUNT(%s.id)', $this->repositoryAlias))
            ->resetDQLPart('orderBy')
            ->resetDQLPart('groupBy')
            ->getQuery()
            ->getSingleScalarResult();

        $this->setQb();

        return (int) $result;
    }

    /**
     * @param QueryInterface $query
     *
     * @return mixed
     */
    public function getResult(QueryInterface $query)
    {
        $qb     = $query->matchDoctrine($this->qb, null);
        $result = $qb->getQuery()->getResult();

        $this->setQb();

        return $result;
    }

    /**
     * Return all instances
     *
     * @return mixed
     */
    public function getAll()
    {
        $all = $this->repository->findAll();

        $this->setQb();

        return array_map(function ($a) {
            return $this->convert($a);
        }, $all);
    }
}
