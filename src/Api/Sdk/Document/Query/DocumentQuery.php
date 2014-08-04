<?php

namespace Api\Sdk\Document\Query;

use Doctrine\ORM\QueryBuilder;
use Api\Sdk\Model\Contract;
use Api\Sdk\Model\Revision;
use Api\Sdk\Query\QueryInterface;
use Api\SdkBundle\Entity\DocumentType;

/**
 * Class DocumentQuery
 */
class DocumentQuery implements QueryInterface
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
        return 'd';
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
                    return $qb->expr()->in('d.id', $value);
                },
            'contract' => function (QueryBuilder $qb, Contract $value) {
                    $qb->innerJoin('d.contracts', 'c');

                    return $qb->expr()->eq('c.id', $value->getId());
                },
            'revision' => function (QueryBuilder $qb, Revision $value) {
                    $qb->innerJoin('d.revisions', 'r');

                    return $qb->expr()->eq('r.id', $value->getId());
                },
            'hasSize' => function (QueryBuilder $qb, $value) {
                    return $qb->expr()->gt('d.size', 0);
                },
            'excludedTypes' => function (QueryBuilder $qb, array $excludedTypes) {
                    $qb->innerJoin('d.type', 't');
                    foreach ($excludedTypes as $typeId) {
                        $qb->andWhere($qb->expr()->neq('t.id', $typeId));
                    }
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
