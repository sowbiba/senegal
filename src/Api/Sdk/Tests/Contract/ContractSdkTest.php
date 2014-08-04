<?php
namespace Api\Sdk\Tests\Contract;

use Api\Sdk\Contract\Query\ContractQuery;
use Api\Sdk\Tests\SdkTestCase;

class ContractSdkTest extends SdkTestCase
{

    public function testGetById()
    {
        $contract = $this->getSdk("contract")->getById(1);

        $this->assertInstanceOf('Api\Sdk\Model\Contract', $contract);
        $this->assertEquals(1, $contract->getId());

        $insurers = $contract->getInsurers();
        $this->assertCount(1, $insurers);

        $insurer = $insurers[0];

        $this->assertInstanceOf('Api\Sdk\Model\Company', $insurer);
        $this->assertEquals('AGF VIE', $insurer->getName());
        $this->assertEquals(6, $insurer->getId());
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testGetByIdWithBadParameter()
    {
        $this->getSdk("contract")->getById("Zephyr");
    }

    public function testGetCollection()
    {
        $contracts = $this->getSdk("contract")->getCollection(new ContractQuery());
        foreach ($contracts as $contract) {
            $this->assertInstanceOf('Api\Sdk\Model\Contract', $contract);
        }
    }

    public function testCount()
    {
        $this->assertTrue(is_integer($this->getSdk("contract")->count(new ContractQuery())));
    }
}
