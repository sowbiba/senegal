<?php

namespace Api\Sdk\User\Query;

use Doctrine\ORM\QueryBuilder;
use Api\Sdk\Model\User;
use Api\Sdk\Query\QueryInterface;

/**
 * Class UserQuery
 */
class UserQuery implements QueryInterface
{
    /**
     * @var array
     */
    protected $filters = array('hasSize' => true);

    /**
     * @param array $filters
     */
    public function __construct(array $filters = array())
    {
        $this->filters = array_merge($this->filters, $filters);
    }

    /**
     * @return string
     */
    public function getRepositoryLetter()
    {
        return 'u';
    }

    /**
     * Build query with sets
     *
     * @param QueryBuilder $qb
     *
     * @return QueryBuilder
     */
    public function matchDoctrine(QueryBuilder $qb)
    {
        $allowedFilters = array(
            'ids' => function (QueryBuilder $qb, array $value) {
                    return $qb->expr()->in('u.id', $value);
                },
            'username' => function (QueryBuilder $qb, $value) {
                    return $qb->expr()->eq('u.username', $value);
                },
            'password' => function (QueryBuilder $qb, $value) {
                    return $qb->expr()->eq('u.password', sha1($value));
                }
        );

        foreach ($allowedFilters as $key => $callback) {
            if (isset($this->filters[$key])) {
                $qb->andWhere($callback($qb, $this->filters[$key]));
            }
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
     * @return array
     */
    public function toArray()
    {
        return $this->filters;
    }
}
