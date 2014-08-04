<?php

namespace Api\Sdk\Query;

use Doctrine\ORM\QueryBuilder;

/**
 *
 * This class is used to sort a query.
 * If you need to use matchPropel don't forget to implement method findColumn
 *
 * Class SortQuery
 */
class SortQuery implements QueryInterface
{
    const SORT_ASC  = 'asc';
    const SORT_DESC = 'desc';

    protected $query;
    protected $sorts;

    /**
     * @param QueryInterface $query
     * @param array          $sorts
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(QueryInterface $query, array $sorts = array())
    {
        $this->query = $query;
        $this->sorts = array();

        foreach ($sorts as $sort) {
            if (!is_array($sort)) {
                throw new \InvalidArgumentException(sprintf('Expected an array (name, ASC), got : ' . var_export($sort, true)));
            }
        }

        $this->sorts = $sorts;
    }

    /**
     * @param \Criteria $criteria
     *
     * @return \Criterion|void
     *
     * @throws \DomainException
     *
     * @codeCoverageIgnore
     */
    public function matchPropel(\Criteria &$criteria)
    {
        throw new \DomainException(sprintf('Method matchPropel not implemented.'));
    }

    /**
     * @param QueryBuilder $qb
     *
     * @throws \DomainException
     *
     * @codeCoverageIgnore
     */
    public function matchDoctrine(QueryBuilder $qb)
    {
        $this->qb = $qb;

        foreach ($this->sorts as $sort) {
            list($column, $this->order) = $sort;
            if (preg_match('/(\w*)\.(\w*)/', $column, $properties)) {
                // Delete first match which is column name
                array_shift($properties);
                $this->createRelationSort($properties);
            } else {
                $this->createSimpleSort($column);
            }
        }

        return $this->query->matchDoctrine($this->qb);
    }

    /**
     * @param        $column
     * @param string $order
     *
     * @return $this
     */
    public function addSort($column, $order = self::SORT_ASC)
    {
        $this->sorts[] = array($column, $order);

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            '_type' => 'sort',
            'sorts' => $this->sorts,
            'query' => $this->query->toArray()
        );
    }

    /**
     * Match from sort name with legacy propel column name
     *
     * @param $column
     *
     * @throws \DomainException
     */
    protected function findColumn($column)
    {
        throw new \DomainException(sprintf('Method findColumn not implemented.'));
    }

    /**
     *
     * Set sort query in query builder which acts on properties of an entity linked to the current one
     *
     * @param array $properties
     *
     * @throws \InvalidArgumentException
     */
    protected function createRelationSort($properties)
    {
        list($relation, $field) = $properties;

        $relationLetter = lcfirst(substr($relation, 0, 1));

        $this->qb
            ->leftJoin(sprintf('%s.%s', $this->query->getRepositoryLetter(), $relation), $relationLetter)
            ->orderBy(sprintf('%s.%s', $relationLetter, $field), $this->order)
        ;
    }

    /**
     * Set sort query in query builder based on a column of the current entity
     *
     * @param string $column entity column
     *
     * @throws \InvalidArgumentException
     */
    protected function createSimpleSort($column)
    {
        $column = sprintf('%s.%s', $this->query->getRepositoryLetter(), $column);

        $this->qb->orderBy($column, $this->order);
    }
}
