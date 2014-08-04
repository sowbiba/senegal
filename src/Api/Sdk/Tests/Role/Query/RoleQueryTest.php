<?php

namespace Api\Sdk\Tests\Role\Query;

use Api\Sdk\Role\Query\RoleQuery;

/**
 * Class RoleQueryTest
 */
class RoleQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the toArray method
     */
    public function testToArray()
    {
        $filters = ['test'=> 'oui'];
        $query = new RoleQuery($filters);
        $this->assertSame($filters, $query->toArray());
    }

    /**
     * Test that the query builder is not altered with no matching filter
     */
    public function testMatchDoctrineWithoutMatchingFilter()
    {
        $query = new RoleQuery(['test'=> 'oui', 'name' => null]);

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $qb->expects($this->never())
            ->method('andWhere');

        $query->matchDoctrine($qb);
    }

    /**
     * The the matchDoctrine function with a valid parameter
     */
    public function testMatchDoctrine()
    {
        $query = new RoleQuery(['name'=> 'espri', 'ids' => [1, 2]]);

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $expr = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr->expects($this->at(0))
            ->method('like')
            ->with('r.name', ':name')
            ->will($this->returnValue('expected result'));

        $expr->expects($this->at(1))
            ->method('in')
            ->with('r.id', [1, 2])
            ->will($this->returnValue('expected result'));

        $qb->expects($this->once())
            ->method('setParameter')
            ->with('name', 'espri%')
            ->will($this->returnValue('expected result'));

        $qb->expects($this->exactly(2))
            ->method('expr')
            ->will($this->returnValue($expr));

        $qb->expects($this->exactly(2))
            ->method('andWhere')
            ->with('expected result');

        $query->matchDoctrine($qb);
    }

    /**
     * Test that matchPropel throw an exception
     *
     * @expectedException \LogicException
     * @expectedExceptionMessage No implementation needed
     */
    public function testMatchPropel()
    {
        $query = new RoleQuery(['test'=> 'oui']);
        $criteria = $this->getMock('\Criteria');
        $query->matchPropel($criteria);
    }
}
