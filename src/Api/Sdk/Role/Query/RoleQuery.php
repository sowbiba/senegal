<?php

namespace Api\Sdk\Role\Query;

use Doctrine\ORM\QueryBuilder;
use Api\Sdk\Query\QueryInterface;

/**
 * Class RoleQuery
 * To make query in roles
 */
class RoleQuery implements QueryInterface
{
    /**
     * Query filters
     *
     * @var array
     */
    protected $filters;

    /**
     * @param array $filters Query filters
     *                       Format [parameter_name => value [, ...]]
     *                       Possible parameters :
     *                       - name (will make a LIKE 'value%', example : ['name' => 'espri'] will filter to keep all role begin with espri)
     *                       - ids : array of role identifiants
     *
     */
    public function __construct(array $filters = array())
    {
        $this->filters = $filters;
    }

    /**
     * Build query with sets
     *
     * @param Doctrine\ORM\QueryBuilder $qb Query builder
     *
     * @return Doctrine\ORM\QueryBuilder The one in parameter populated with filters
     */
    public function matchDoctrine(QueryBuilder $qb)
    {
        if (isset($this->filters['name'])) {
            $qb->setParameter('name', $this->filters['name'] . '%');
            $qb->andWhere($qb->expr()->like('r.name', ':name'));
        }

        if (isset($this->filters['ids'])) {
            $qb->andWhere($qb->expr()->in('r.id', $this->filters['ids']));
        }

        return $qb;
    }

    /**
     * {@inherited}
     */
    public function matchPropel(\Criteria &$criteria)
    {
        throw new \LogicException('No implementation needed');
    }

    /**
     * Returns the role query in array format
     *
     * @return array
     */
    public function toArray()
    {
        return $this->filters;
    }
}
