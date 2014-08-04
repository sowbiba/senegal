<?php

namespace Api\Sdk\Tests\Query;

use Doctrine\ORM\QueryBuilder;
use Api\Sdk\Query\QueryInterface;
use Api\Sdk\Query\SortQuery;

class SortQueryTest extends \PHPUnit_Framework_TestCase
{
    public function testAddSort()
    {
        $query = new SortQuery(new MockableQuery(),
            array(
                array('name', SortQuery::SORT_DESC)
            )
        );

        $query->addSort('isActive');
        $expected = array(
            '_type' => 'sort',
            'sorts' => array(
                array('name', SortQuery::SORT_DESC),
                array('isActive', SortQuery::SORT_ASC)
            ),
            'query' => null
        );

        $this->assertEquals($expected, $query->toArray());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Expected an array (name, ASC), got : 'name'
     */
    public function testInvalidArgument()
    {
        new SortQuery(new MockableQuery(), array('name'));
    }
}

class MockableQuery implements QueryInterface
{
    public function matchDoctrine(QueryBuilder $qb) {}
    public function matchPropel(\Criteria &$criteria) {}
    public function toArray() {}
}
