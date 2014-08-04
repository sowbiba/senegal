<?php

namespace Api\Sdk\Tests\Contract\Query;

use Doctrine\ORM\Query\Expr\Join;
use Api\Sdk\Contract\Query\ContractQuery;
use Api\Sdk\Model\Contract;
use Api\Sdk\Model\Revision;

/**
 * Class ContractQueryTest
 */
class ContractQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the toArray method
     */
    public function testToArray()
    {
        $query = new ContractQuery(array('test'=> 'oui', 'obsoleteProductLine' => true));
        $expected = array('future' => false, 'obsoleteProductLine' => true, 'test'=> 'oui');
        $this->assertSame($expected, $query->toArray());
    }

    /**
     * Test that the query builder is not altered with no matching filter
     */
    public function testMatchDoctrineWithoutMatchingFilter()
    {
        $query = new ContractQuery(array('test'=> 'oui', 'future' => null, 'obsoleteProductLine' => null));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $qb->expects($this->never())
            ->method('andWhere');

        $query->matchDoctrine($qb);
    }

    /**
     * Test the matchDoctrine function with active flag
     */
    public function testMatchDoctrineWithActive()
    {
        $query = new ContractQuery(array('active'=> true, 'future' => null, 'obsoleteProductLine' => null));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $expr = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr->expects($this->once())
            ->method('eq')
            ->with('c.isActive', true)
            ->will($this->returnValue('expected result'));

        $qb->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($expr));

        $qb->expects($this->once())
            ->method('andWhere')
            ->with('expected result');

        $query->matchDoctrine($qb);
    }

    /**
     * Test the matchDoctrine function with future flag
     */
    public function testMatchDoctrineWithFuture()
    {
        $query = new ContractQuery(array('future'=> true, 'obsoleteProductLine' => null));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $expr = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr->expects($this->once())
            ->method('eq')
            ->with('c.isFuture', 1)
            ->will($this->returnValue('expected result'));

        $qb->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($expr));

        $qb->expects($this->once())
            ->method('andWhere')
            ->with('expected result');

        $query->matchDoctrine($qb);
    }

    /**
     * Test the matchDoctrine function with marketed flag
     */
    public function testMatchDoctrineWithMarketed()
    {
        $query = new ContractQuery(array('marketed'=> true, 'future' => null, 'obsoleteProductLine' => null));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $expr = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr->expects($this->once())
            ->method('eq')
            ->with('c.isMarketed', true)
            ->will($this->returnValue('expected result'));

        $qb->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($expr));

        $qb->expects($this->once())
            ->method('andWhere')
            ->with('expected result');

        $query->matchDoctrine($qb);
    }

    /**
     * Test the matchDoctrine function with name
     */
    public function testMatchDoctrineWithName()
    {
        $query = new ContractQuery(array('name'=> 'toto', 'future' => null, 'obsoleteProductLine' => null));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $expr = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr->expects($this->at(0))
            ->method('like')
            ->with('c.name', ':name')
            ->will($this->returnValue('non expected result'));

        $expr->expects($this->at(1))
            ->method('like')
            ->with('c.planName', ':name')
            ->will($this->returnValue('non expected result'));

        $expr->expects($this->once())
            ->method('orX')
            ->will($this->returnValue('expected result'));

        $qb->expects($this->any())
            ->method('expr')
            ->will($this->returnValue($expr));

        $qb->expects($this->once())
            ->method('setParameter')
            ->with('name', '%toto%');

        $qb->expects($this->once())
            ->method('andWhere')
            ->with('expected result');

        $query->matchDoctrine($qb);
    }

    /**
     * Test the matchDoctrine function with inheritance
     */
    public function testMatchDoctrineWithChildInheritance()
    {
        $query = new ContractQuery(array('inheritance'=> Contract::CHILD_INHERITANCE, 'future' => null, 'obsoleteProductLine' => null));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $expr = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr->expects($this->once())
            ->method('isNotNull')
            ->with('c.parentId')
            ->will($this->returnValue('expected result'));

        $qb->expects($this->any())
            ->method('expr')
            ->will($this->returnValue($expr));

        $qb->expects($this->once())
            ->method('andWhere')
            ->with('expected result');

        $query->matchDoctrine($qb);
    }

    /**
     * Test the matchDoctrine function with inheritance
     */
    public function testMatchDoctrineWithParentInheritance()
    {
        $query = new ContractQuery(array('inheritance'=> Contract::NOT_CHILD_INHERITANCE, 'future' => null, 'obsoleteProductLine' => null));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $expr = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr->expects($this->once())
            ->method('isNull')
            ->with('c.parentId')
            ->will($this->returnValue('expected result'));

        $qb->expects($this->any())
            ->method('expr')
            ->will($this->returnValue($expr));

        $qb->expects($this->once())
            ->method('andWhere')
            ->with('expected result');

        $query->matchDoctrine($qb);
    }

    /**
     * Test the matchDoctrine function with obsolete ProductLine
     */
    public function testMatchDoctrineWithObsoleteProductLine()
    {
        $query = new ContractQuery(array('obsoleteProductLine'=> true, 'future' => null));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $expr = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr->expects($this->once())
            ->method('eq')
            ->with('o.isObsolete', 1)
            ->will($this->returnValue('expected result'));

        $qb->expects($this->any())
            ->method('expr')
            ->will($this->returnValue($expr));

        $qb->expects($this->once())
            ->method('andWhere')
            ->with('expected result');

        $qb->expects($this->once())
            ->method('leftJoin')
            ->with('c.productLine', 'o');

        $query->matchDoctrine($qb);
    }

    /**
     * Test the matchDoctrine function with productLine
     */
    public function testMatchDoctrineWithProductLine()
    {
        $productLine = $this->getMockBuilder('Api\Sdk\Model\ProductLine')
            ->disableOriginalConstructor()
            ->getMock();

        $productLine->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(42));

        $query = new ContractQuery(array('productLine'=> $productLine, 'future' => null, 'obsoleteProductLine' => null));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $expr = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr->expects($this->once())
            ->method('eq')
            ->with('p.id', 42)
            ->will($this->returnValue('expected result'));

        $qb->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($expr));

        $qb->expects($this->once())
            ->method('andWhere')
            ->with('expected result');

        $qb->expects($this->once())
            ->method('innerJoin')
            ->with('c.productLine', 'p');

        $query->matchDoctrine($qb);
    }

    /**
     * Test the matchDoctrine function with distributor
     */
    public function testMatchDoctrineWithDistributor()
    {
        $distributor = $this->getMockBuilder('Api\Sdk\Model\Company')
            ->disableOriginalConstructor()
            ->getMock();

        $distributor->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(42));

        $query = new ContractQuery(array('distributor'=> $distributor, 'future' => null, 'obsoleteProductLine' => null));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $expr = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr->expects($this->once())
            ->method('eq')
            ->with('d.id', 42)
            ->will($this->returnValue('expected result'));

        $qb->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($expr));

        $qb->expects($this->once())
            ->method('andWhere')
            ->with('expected result');

        $qb->expects($this->once())
            ->method('innerJoin')
            ->with('c.distributors', 'd');

        $query->matchDoctrine($qb);
    }

    /**
     * Test the matchDoctrine function with insurer
     */
    public function testMatchDoctrineWithInsurer()
    {
        $insurer = $this->getMockBuilder('Api\Sdk\Model\Company')
            ->disableOriginalConstructor()
            ->getMock();

        $insurer->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(42));

        $query = new ContractQuery(array('insurer'=> $insurer, 'future' => null, 'obsoleteProductLine' => null));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $expr = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr->expects($this->once())
            ->method('eq')
            ->with('i.id', 42)
            ->will($this->returnValue('expected result'));

        $qb->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($expr));

        $qb->expects($this->once())
            ->method('andWhere')
            ->with('expected result');

        $qb->expects($this->once())
            ->method('innerJoin')
            ->with('c.insurers', 'i');

        $query->matchDoctrine($qb);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Value "test" for filter "Révision" is unknown
     */
    public function testMatchDoctrineWithRevisionStatusUnknown()
    {
        $query = new ContractQuery(array('revision_status'=> 'test', 'future' => null, 'obsoleteProductLine' => null));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $query->matchDoctrine($qb);
    }

    /**
     * Test the matchDoctrine function with revision_status in case of in-progress
     */
    public function testMatchDoctrineWithRevisionStatusInProgress()
    {
        $query = new ContractQuery(array(
            'revision_status'=> 'in-progress',
            'future' => null,
            'obsoleteProductLine' => null
        ));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $expr1 = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr2 = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $exprAndX = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr1->expects($this->once())
            ->method('eq')
            ->with('p_revision_status.isRevisionable', 1)
            ->will($this->returnValue('expr-1'));

        $expr2->expects($this->once())
            ->method('eq')
            ->with('cr_filter.status', Revision::STATUS_IN_PROGRESS)
            ->will($this->returnValue('expr-2'));

        $exprAndX->expects($this->once())
            ->method('andX')
            ->with('expr-1', 'expr-2')
            ->will($this->returnValue('expected result'));

        $qb->expects($this->at(2))
            ->method('expr')
            ->will($this->returnValue($exprAndX));

        $qb->expects($this->at(3))
            ->method('expr')
            ->will($this->returnValue($expr1));

        $qb->expects($this->at(4))
            ->method('expr')
            ->will($this->returnValue($expr2));

        $qb->expects($this->once())
            ->method('andWhere')
            ->with('expected result');

        $qb->expects($this->at(0))
            ->method('innerJoin')
            ->with('c.productLine', 'p_revision_status');

        $qb->expects($this->at(1))
            ->method('innerJoin')
            ->with('c.revisions', 'cr_filter');

        $query->matchDoctrine($qb);
    }

    /**
     * Test the matchDoctrine function with revision_status in case of submitted
     */
    public function testMatchDoctrineWithRevisionStatusSubmitted()
    {
        $query = new ContractQuery(array(
            'revision_status'=> 'submitted',
            'future' => null,
            'obsoleteProductLine' => null
        ));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $expr1 = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr2 = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $exprAndX = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr1->expects($this->once())
            ->method('eq')
            ->with('p_revision_status.isRevisionable', 1)
            ->will($this->returnValue('expr-1'));

        $expr2->expects($this->once())
            ->method('eq')
            ->with('cr_filter.status', Revision::STATUS_SUBMITTED)
            ->will($this->returnValue('expr-2'));

        $exprAndX->expects($this->once())
            ->method('andX')
            ->with('expr-1', 'expr-2')
            ->will($this->returnValue('expected result'));

        $qb->expects($this->at(2))
            ->method('expr')
            ->will($this->returnValue($exprAndX));

        $qb->expects($this->at(3))
            ->method('expr')
            ->will($this->returnValue($expr1));

        $qb->expects($this->at(4))
            ->method('expr')
            ->will($this->returnValue($expr2));

        $qb->expects($this->once())
            ->method('andWhere')
            ->with('expected result');

        $qb->expects($this->at(0))
            ->method('innerJoin')
            ->with('c.productLine', 'p_revision_status');

        $qb->expects($this->at(1))
            ->method('innerJoin')
            ->with('c.revisions', 'cr_filter');

        $query->matchDoctrine($qb);
    }

    /**
     * Test the matchDoctrine function with revision_status in case of no-revision
     */
    public function testMatchDoctrineWithRevisionStatusNoRevision()
    {
        $query = new ContractQuery(array(
            'revision_status'=> 'no-revision',
            'future' => null,
            'obsoleteProductLine' => null
        ));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $expr1 = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr2 = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $exprAndX = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr1->expects($this->once())
            ->method('eq')
            ->with('p_revision_status.isRevisionable', 1)
            ->will($this->returnValue('expr-1'));

        $expr2->expects($this->once())
            ->method('isNull')
            ->with('cr_filter.id')
            ->will($this->returnValue('expr-2'));

        $exprAndX->expects($this->once())
            ->method('andX')
            ->with('expr-1', 'expr-2')
            ->will($this->returnValue('expected result'));

        $qb->expects($this->at(2))
            ->method('expr')
            ->will($this->returnValue($exprAndX));

        $qb->expects($this->at(3))
            ->method('expr')
            ->will($this->returnValue($expr1));

        $qb->expects($this->at(4))
            ->method('expr')
            ->will($this->returnValue($expr2));

        $qb->expects($this->once())
            ->method('andWhere')
            ->with('expected result');

        $qb->expects($this->once())
            ->method('innerJoin')
            ->with('c.productLine', 'p_revision_status');

        $qb->expects($this->once())
            ->method('leftJoin')
            ->with('c.revisions', 'cr_filter');

        $query->matchDoctrine($qb);
    }

    /**
     * Test the matchDoctrine function with revision_status in case of published
     */
    public function testMatchDoctrineWithRevisionStatusPublished()
    {
        $query = new ContractQuery(array(
            'revision_status'=> 'published',
            'future' => null,
            'obsoleteProductLine' => null
        ));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $expr1 = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr2 = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr3 = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $exprAndX = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr1->expects($this->once())
            ->method('eq')
            ->with('p_revision_status.isRevisionable', 1)
            ->will($this->returnValue('expr-1'));

        $expr2->expects($this->once())
            ->method('eq')
            ->with('cr_filter.status', Revision::STATUS_PUBLISHED)
            ->will($this->returnValue('expr-2'));

        $expr3->expects($this->once())
            ->method('isNull')
            ->with('cr_in_progress.id')
            ->will($this->returnValue('expr-3'));

        $exprAndX->expects($this->once())
            ->method('andX')
            ->with('expr-1', 'expr-2', 'expr-3')
            ->will($this->returnValue('expected result'));

        $qb->expects($this->at(3))
            ->method('expr')
            ->will($this->returnValue($exprAndX));

        $qb->expects($this->at(4))
            ->method('expr')
            ->will($this->returnValue($expr1));

        $qb->expects($this->at(5))
            ->method('expr')
            ->will($this->returnValue($expr2));

        $qb->expects($this->at(6))
            ->method('expr')
            ->will($this->returnValue($expr3));

        $qb->expects($this->once())
            ->method('andWhere')
            ->with('expected result');

        $qb->expects($this->at(0))
            ->method('innerJoin')
            ->with('c.productLine', 'p_revision_status');

        $qb->expects($this->at(1))
            ->method('innerJoin')
            ->with('c.revisions', 'cr_filter');

        $qb->expects($this->at(2))
            ->method('leftJoin')
            ->with('c.revisions', 'cr_in_progress', Join::WITH, 'cr_in_progress.status = '.Revision::STATUS_IN_PROGRESS);

        $query->matchDoctrine($qb);
    }

    /**
     * Test the matchDoctrine function with revision_status in case of in-progress-or-published
     */
    public function testMatchDoctrineWithRevisionStatusPublishedOrInProgress()
    {
        $query = new ContractQuery(array(
            'revision_status'=> 'in-progress-or-published',
            'future' => null,
            'obsoleteProductLine' => null
        ));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $expr1 = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr2 = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $exprAndX = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr1->expects($this->once())
            ->method('eq')
            ->with('p_revision_status.isRevisionable', 1)
            ->will($this->returnValue('expr-1'));

        $expr2->expects($this->once())
            ->method('in')
            ->with('cr_filter.status', array(Revision::STATUS_IN_PROGRESS, Revision::STATUS_PUBLISHED, Revision::STATUS_SUBMITTED, Revision::STATUS_PENDING_PUBLICATION))
            ->will($this->returnValue('expr-2'));

        $exprAndX->expects($this->once())
            ->method('andX')
            ->with('expr-1', 'expr-2')
            ->will($this->returnValue('expected result'));

        $qb->expects($this->at(2))
            ->method('expr')
            ->will($this->returnValue($exprAndX));

        $qb->expects($this->at(3))
            ->method('expr')
            ->will($this->returnValue($expr1));

        $qb->expects($this->at(4))
            ->method('expr')
            ->will($this->returnValue($expr2));

        $qb->expects($this->once())
            ->method('andWhere')
            ->with('expected result');

        $qb->expects($this->at(0))
            ->method('innerJoin')
            ->with('c.productLine', 'p_revision_status');

        $qb->expects($this->at(1))
            ->method('innerJoin')
            ->with('c.revisions', 'cr_filter');

        $query->matchDoctrine($qb);
    }

    /**
     * Test the matchDoctrine function with revision_status in case of not-revisionable
     */
    public function testMatchDoctrineWithRevisionStatusNotRevisionable()
    {
        $query = new ContractQuery(array(
            'revision_status'=> 'not-revisionable',
            'future' => null,
            'obsoleteProductLine' => null
        ));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $expr = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr->expects($this->once())
            ->method('eq')
            ->with('p_revision_status.isRevisionable', 0)
            ->will($this->returnValue('expected result'));

        $qb->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($expr));

        $qb->expects($this->once())
            ->method('andWhere')
            ->with('expected result');

        $qb->expects($this->once())
            ->method('innerJoin')
            ->with('c.productLine', 'p_revision_status');

        $query->matchDoctrine($qb);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage No implementation needed
     */
    public function testMatchPropel()
    {
        $query = new ContractQuery(array('test'=> 'oui'));
        $criteria = $this->getMock('\Criteria');
        $query->matchPropel($criteria);
    }
}
