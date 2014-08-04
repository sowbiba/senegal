<?php

namespace Api\Sdk\Tests\Contract\Query;

use Doctrine\ORM\QueryBuilder;
use Api\Sdk\Contract\Query\ContractSortQuery;
use Api\Sdk\Query\QueryInterface;
use Api\Sdk\Query\SortQuery;

/**
 * Class ContractSortQueryTest
 */
class ContractSortQueryTest extends \PHPUnit_Framework_TestCase
{
    public function testMatchDoctrineWithRevisionStatus()
    {
        $query = new ContractSortQuery(new MockableQuery(),
            array(
                array('revision_status', SortQuery::SORT_ASC)
            )
        );

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $qb->expects($this->once())
            ->method('addSelect')
            ->with('
                CASE WHEN (cpl.isRevisionable = 0) THEN 6 ELSE
                    CASE WHEN (COUNT(cr.id) = 0) THEN 5 ELSE
                        CASE WHEN (cr_published.id IS NOT NULL) THEN 4 ELSE
                            CASE WHEN (cr_pending_publication.id IS NOT NULL) THEN 3 ELSE
                                CASE WHEN (cr_submitted.id IS NOT NULL) THEN 2 ELSE 1 END
                        END
                    END
                END AS revision_status')
            ->will($this->returnValue($qb));

        $qb->expects($this->at(1))
            ->method('leftJoin')
            ->with('test.revisions', 'cr')
            ->will($this->returnValue($qb));

        $qb->expects($this->at(2))
            ->method('leftJoin')
            ->with('test.revisions', 'cr_submitted', 'WITH', 'cr_submitted.status = 3')
            ->will($this->returnValue($qb));

        $qb->expects($this->at(3))
            ->method('leftJoin')
            ->with('test.revisions', 'cr_pending_publication', 'WITH', 'cr_pending_publication.status = 4')
            ->will($this->returnValue($qb));

        $qb->expects($this->at(4))
            ->method('leftJoin')
            ->with('test.revisions', 'cr_published', 'WITH', 'cr_published.status = 1')
            ->will($this->returnValue($qb));

        $qb->expects($this->at(5))
            ->method('leftJoin')
            ->with('test.productLine', 'cpl')
            ->will($this->returnValue($qb));

        $qb->expects($this->once())
            ->method('groupBy')
            ->with('test.id')
            ->will($this->returnValue($qb));

        $qb->expects($this->once())
            ->method('orderBy')
            ->with('revision_status', SortQuery::SORT_ASC);

        $query->matchDoctrine($qb);
    }
}

class MockableQuery implements QueryInterface
{
    public function matchDoctrine(QueryBuilder $qb) {}
    public function matchPropel(\Criteria &$criteria) {}
    public function getRepositoryLetter() {return 'test';}
    public function toArray() {}
}
