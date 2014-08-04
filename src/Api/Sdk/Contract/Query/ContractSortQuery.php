<?php

namespace Api\Sdk\Contract\Query;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Api\Sdk\Model\Revision;
use Api\Sdk\Query\SortQuery;

/**
 * Class ContractSortQuery
 *
 * Handle specific sort queries
 */
class ContractSortQuery extends SortQuery
{
    protected $revisionStatusOrder;

    /**
     * @param QueryBuilder $qb
     *
     * @return QueryBuilder
     */
    public function matchDoctrine(QueryBuilder $qb)
    {
        foreach ($this->sorts as $key => $sort) {
            if ($sort[0] === 'revision_status') {
                $this->revisionStatusOrder = $sort[1];
                unset($this->sorts[$key]);
                break;
            }
        }

        if (null !== $this->revisionStatusOrder) {
            $prefix = $this->query->getRepositoryLetter();
            $qb
                ->addSelect('
                CASE WHEN (cpl.isRevisionable = 0) THEN 6 ELSE
                    CASE WHEN (COUNT(cr.id) = 0) THEN 5 ELSE
                        CASE WHEN (cr_published.id IS NOT NULL) THEN 4 ELSE
                            CASE WHEN (cr_pending_publication.id IS NOT NULL) THEN 3 ELSE
                                CASE WHEN (cr_submitted.id IS NOT NULL) THEN 2 ELSE 1 END
                        END
                    END
                END AS revision_status')
                ->leftJoin(sprintf('%s.revisions', $prefix), 'cr')
                ->leftJoin(sprintf('%s.revisions', $prefix), 'cr_submitted', Join::WITH, 'cr_submitted.status = '.Revision::STATUS_SUBMITTED)
                ->leftJoin(sprintf('%s.revisions', $prefix), 'cr_pending_publication', Join::WITH, 'cr_pending_publication.status = '.Revision::STATUS_PENDING_PUBLICATION)
                ->leftJoin(sprintf('%s.revisions', $prefix), 'cr_published', Join::WITH, 'cr_published.status = '.Revision::STATUS_PUBLISHED)
                ->leftJoin(sprintf('%s.productLine', $prefix), 'cpl')
                ->groupBy(sprintf('%s.id', $prefix))
                ->orderBy('revision_status', $this->revisionStatusOrder)
            ;
        }

        return parent::matchDoctrine($qb);
    }
}
