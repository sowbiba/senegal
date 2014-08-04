<?php

namespace Api\Sdk\Tests\ProductLine\Connector\Doctrine;

/**
 * In PHPUnit, $this->getMockBuilder('\PDOStatement') return the
 * PDOException : "You cannot serialize or unserialize PDO instances"
 *
 * This mock class which extends \PDOStatement resolves this problem
 *
 * use like this :
 * $this->getMockBuilder('\Api\Sdk\Tests\ProductLine\Connector\Doctrine\PDOStatementMock')
 *      ->getMock();
 *
 */
class PDOStatementMock extends \PDOStatement
{
    public function __construct()
    {

    }
}
