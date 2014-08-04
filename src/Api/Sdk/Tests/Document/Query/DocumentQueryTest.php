<?php

namespace Api\Sdk\Tests\Document\Query;

use Api\Sdk\Document\Query\DocumentQuery;

/**
 * Class DocumentQueryTest
 */
class DocumentQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the toArray method
     */
    public function testToArray()
    {
        $query = new DocumentQuery(array('test'=> 'oui'));
        $this->assertSame(array('hasSize' => true, 'test'=> 'oui'), $query->toArray());
    }

    /**
     * Test that the query builder is not altered with no matching filter
     */
    public function testMatchDoctrineWithoutMatchingFilter()
    {
        $query = new DocumentQuery(array('test'=> 'oui', 'hasSize' => null));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $qb->expects($this->never())
            ->method('andWhere');

        $query->matchDoctrine($qb);
    }

    /**
     * The the matchDoctrine function with ids
     */
    public function testMatchDoctrineWithIds()
    {
        $query = new DocumentQuery(array('ids'=> array(1, 2, 3), 'hasSize' => null));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $expr = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr->expects($this->once())
            ->method('in')
            ->with('d.id', array(1, 2, 3))
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
     * The the matchDoctrine function with contract
     */
    public function testMatchDoctrineWithContract()
    {
        $contract = $this->getMockBuilder('Api\Sdk\Model\Contract')
            ->disableOriginalConstructor()
            ->getMock();

        $contract->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(42));

        $query = new DocumentQuery(array('contract'=> $contract, 'hasSize' => null));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $expr = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr->expects($this->once())
            ->method('eq')
            ->with('c.id', 42)
            ->will($this->returnValue('expected result'));

        $qb->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($expr));

        $qb->expects($this->once())
            ->method('andWhere')
            ->with('expected result');

        $qb->expects($this->once())
            ->method('innerJoin')
            ->with('d.contracts', 'c');

        $query->matchDoctrine($qb);
    }

    /**
     * The the matchDoctrine function with revision
     */
    public function testMatchDoctrineWithRevision()
    {
        $revision = $this->getMockBuilder('Api\Sdk\Model\Revision')
            ->disableOriginalConstructor()
            ->getMock();

        $revision->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(42));

        $query = new DocumentQuery(array('revision'=> $revision, 'hasSize' => null));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $expr = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr->expects($this->once())
            ->method('eq')
            ->with('r.id', 42)
            ->will($this->returnValue('expected result'));

        $qb->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($expr));

        $qb->expects($this->once())
            ->method('andWhere')
            ->with('expected result');

        $qb->expects($this->once())
            ->method('innerJoin')
            ->with('d.revisions', 'r');

        $query->matchDoctrine($qb);
    }

    /**
     * The the matchDoctrine function with revision
     */
    public function testMatchDoctrineWithHasSize()
    {
        $query = new DocumentQuery(array('hasSize'=> true));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $expr = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr->expects($this->once())
            ->method('gt')
            ->with('d.size', 0)
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
     * @expectedException \LogicException
     * @expectedExceptionMessage No implementation needed
     */
    public function testMatchPropel()
    {
        $query = new DocumentQuery(array('test'=> 'oui'));
        $criteria = $this->getMock('\Criteria');
        $query->matchPropel($criteria);
    }

}
