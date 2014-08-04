<?php
/**
 * Author: Florent Coquel
 * Date: 26/09/13
 */

namespace Api\Sdk\Tests\Connector;

use Api\Sdk\Contract\Connector\Data\ContractDataConnector;

class AbstractConnectorTest extends \PHPUnit_Framework_TestCase
{
    private $connector;

    protected function setUp()
    {
        parent::setUp();
        $this->connector = new ContractDataConnector();

    }

    public function testSetLogger()
    {
        $logger = $this->getMockBuilder('Monolog\Logger')
            ->disableOriginalConstructor()
            ->getMock();

        $this->connector->setLogger($logger);
    }

    public function testImplementedMethod()
    {
        $this->connector->getById(1);
    }

    /**
     * @expectedException Api\Sdk\Connector\NotImplementedException
     */
    public function testNotImplementedMethod()
    {
        $this->connector->prout();
    }
}
