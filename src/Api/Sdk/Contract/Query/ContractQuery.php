<?php

namespace Api\Sdk\Contract\Query;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Api\Sdk\Model\Company;
use Api\Sdk\Model\CompanyGroup;
use Api\Sdk\Model\Contract;
use Api\Sdk\Model\ProductLine;
use Api\Sdk\Model\Revision;
use Api\Sdk\Query\QueryInterface;

/**
 * Class ContractQuery
 */
class ContractQuery implements QueryInterface
{
    const REVISION_STATUS_IN_PROGRESS         = 'in-progress';
    const REVISION_STATUS_SUBMITTED           = 'submitted';
    const REVISION_STATUS_PENDING_PUBLICATION = 'pending-publication';
    const REVISION_STATUS_PUBLISHED           = 'published';
    const REVISION_STATUS_ALL                 = 'in-progress-or-published';
    const REVISION_STATUS_NO_REVISION         = 'no-revision';
    const REVISION_STATUS_NOT_REVISIONABLE    = 'not-revisionable';

    /**
     * @var array
     */
    protected $filters = array('future' => false, 'obsoleteProductLine' => false);

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
        return 'c';
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
            'active'   => function (QueryBuilder $qb, $value) {return $qb->expr()->eq('c.isActive', $value);},
            'marketed' => function (QueryBuilder $qb, $value) {return $qb->expr()->eq('c.isMarketed', $value);},
            'name'   => function (QueryBuilder $qb, $value) {
                $qb->setParameter('name', '%'.$value.'%');

                return $qb->expr()->orX($qb->expr()->like('c.name', ':name'), $qb->expr()->like('c.planName', ':name'));
            },
            'inheritance' => function (QueryBuilder $qb, $value) {
                return $value === Contract::CHILD_INHERITANCE ? $qb->expr()->isNotNull('c.parentId') : $qb->expr()->isNull('c.parentId');
            },
            'obsoleteProductLine' => function (QueryBuilder $qb, $value) {
                $qb->leftJoin('c.productLine', 'o');

                return $qb->expr()->eq('o.isObsolete', (int) $value);
            },
            'future' => function (QueryBuilder $qb, $value) {
                return $qb->expr()->eq('c.isFuture', (int) $value);
            },
            'productLine' => function (QueryBuilder $qb, ProductLine $value) {
                $qb->innerJoin('c.productLine', 'p');

                return $qb->expr()->eq('p.id', $value->getId());
            },
            'distributor' => function (QueryBuilder $qb, Company $value) {
                $qb->innerJoin('c.distributors', 'd');

                return $qb->expr()->eq('d.id', $value->getId());
            },
            'distributorGroup' => function (QueryBuilder $qb, CompanyGroup $value) {
                $qb->innerJoin('c.distributors', 'd')
                   ->innerJoin('d.groupesocietes', 'g');

                return $qb->expr()->eq('g.id', $value->getId());
            },
            'insurer' => function (QueryBuilder $qb, Company $value) {
                $qb->innerJoin('c.insurers', 'i');

                return $qb->expr()->eq('i.id', $value->getId());
            },
            'revision_status' => function (QueryBuilder $qb, $value) {
                $qb->innerJoin('c.productLine', 'p_revision_status');

                switch ($value) {
                    case static::REVISION_STATUS_IN_PROGRESS:
                        $qb->innerJoin('c.revisions', 'cr_filter');

                        return $qb->expr()->andX(
                            $qb->expr()->eq('p_revision_status.isRevisionable', 1),
                            $qb->expr()->eq('cr_filter.status', Revision::STATUS_IN_PROGRESS)
                        );
                    case static::REVISION_STATUS_SUBMITTED:
                        $qb->innerJoin('c.revisions', 'cr_filter');

                        return $qb->expr()->andX(
                            $qb->expr()->eq('p_revision_status.isRevisionable', 1),
                            $qb->expr()->eq('cr_filter.status', Revision::STATUS_SUBMITTED)
                        );
                    case static::REVISION_STATUS_PENDING_PUBLICATION:
                        $qb->innerJoin('c.revisions', 'cr_filter');

                        return $qb->expr()->andX(
                            $qb->expr()->eq('p_revision_status.isRevisionable', 1),
                            $qb->expr()->eq('cr_filter.status', Revision::STATUS_PENDING_PUBLICATION)
                        );
                    case static::REVISION_STATUS_NO_REVISION:
                        $qb->leftJoin('c.revisions', 'cr_filter');

                        return $qb->expr()->andX(
                            $qb->expr()->eq('p_revision_status.isRevisionable', 1),
                            $qb->expr()->isNull('cr_filter.id')
                        );
                    case static::REVISION_STATUS_PUBLISHED:
                        $qb->innerJoin('c.revisions', 'cr_filter');
                        $qb->leftJoin('c.revisions', 'cr_in_progress', Join::WITH, 'cr_in_progress.status = '.Revision::STATUS_IN_PROGRESS);

                        return $qb->expr()->andX(
                            $qb->expr()->eq('p_revision_status.isRevisionable', 1),
                            $qb->expr()->eq('cr_filter.status', Revision::STATUS_PUBLISHED),
                            $qb->expr()->isNull('cr_in_progress.id')
                        );
                    case static::REVISION_STATUS_ALL:
                        $qb->innerJoin('c.revisions', 'cr_filter');

                        return $qb->expr()->andX(
                            $qb->expr()->eq('p_revision_status.isRevisionable', 1),
                            $qb->expr()->in('cr_filter.status', array(Revision::STATUS_IN_PROGRESS, Revision::STATUS_PUBLISHED, Revision::STATUS_SUBMITTED, Revision::STATUS_PENDING_PUBLICATION))
                        );
                    case static::REVISION_STATUS_NOT_REVISIONABLE:
                        return $qb->expr()->eq('p_revision_status.isRevisionable', 0);
                    default:

                        throw new \LogicException(sprintf('Value "%s" for filter "Révision" is unknown', $value));
                }
            },
            'parent' => function (QueryBuilder $qb, Contract $contract) {
                return $qb->expr()->eq('c.parentId', $contract->getId());
            },
            'ignore'   => function (QueryBuilder $qb, Contract $contract) { return $qb->expr()->neq('c.id', $contract->getId()); },
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
