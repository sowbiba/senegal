<?php
namespace Api\Sdk\Tests;

use Api\Sdk\Connector\DataConnector;
use Api\Sdk\Contract\ContractSdk;
use Api\Sdk\Mediator\SdkMediator;
use Api\Sdk\ProductLine\ProductLineSdk;
use Api\Sdk\User\UserSdk;

class SdkMediatorTest extends \PHPUnit_Framework_TestCase
{
    private $logger;
    private $connector;
    private $sdkMediator;

    protected function setUp()
    {
        parent::setUp();
        $this->logger     = $this->getMockBuilder('Monolog\Logger')
            ->disableOriginalConstructor()
            ->getMock();
        $this->connector  = new DataConnector();
        $this->sdkMediator = new SdkMediator($this->logger);
    }

    public function testAddSdk()
    {

        $sdk = new ContractSdk($this->connector);
        $this->sdkMediator->addSdk($sdk);

        $this->assertSame($sdk, $this->sdkMediator->getSdk("contract"));
    }

    public function testSetSdkList()
    {
        $sdks = array(
            "contract"    => new ContractSdk($this->connector),
            "productLine" => new ProductLineSdk($this->connector),
            "user"        => new UserSdk($this->connector),
        );

        $this->sdkMediator->setSdkList($sdks);

        $this->assertSame($sdks["contract"], $this->sdkMediator->getSdk("contract"));
        $this->assertSame($sdks["productLine"], $this->sdkMediator->getSdk("productLine"));
        $this->assertSame($sdks["user"], $this->sdkMediator->getSdk("user"));
    }

    /**
     * @expectedException Exception
     */
    public function testSetSdkListWithBadParameter()
    {
        $this->sdkMediator->setSdkList(array("contract", "productLine"));
    }

    /**
     * @expectedException Exception
     */
    public function testGetSdkWithBadSdk()
    {
        $this->sdkMediator->getSdk("contract");
    }

}
