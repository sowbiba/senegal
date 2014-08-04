<?php

namespace Api\Sdk\Query;

use Doctrine\ORM\QueryBuilder;

class PaginateQuery implements QueryInterface
{
    protected $query;
    protected $limit;
    protected $offset;

    public function __construct(QueryInterface $query, $offset = null, $limit = null)
    {
        $this->query  = $query;
        $this->offset = $offset;
        $this->limit  = $limit;
    }

    /**
     * @param QueryBuilder $qb
     *
     * @return mixed|void
     *
     * @codeCoverageIgnore
     */
    public function matchDoctrine(QueryBuilder $qb)
    {
        if (null !== $this->limit) {
            $qb->setMaxResults($this->limit);
        }

        if (null !== $this->offset) {
            $qb->setFirstResult($this->offset);
        }

        return $this->query->matchDoctrine($qb);
    }

    /**
     * @param \Criteria $criteria
     *
     * @return \Criterion|void
     *
     * @codeCoverageIgnore
     */
    public function matchPropel(\Criteria &$criteria)
    {
        if (null !== $this->limit) {
            $criteria->setLimit($this->limit);
        }

        if (null !== $this->offset) {
            $criteria->setOffset($this->offset);
        }

        return $this->query->matchPropel($criteria);
    }

    public function toArray()
    {
        return array(
            '_type'  => 'paginate',
            'offset' => $this->offset,
            'limit'  => $this->limit,
            'query'  => $this->query->toArray()
        );
    }
}
