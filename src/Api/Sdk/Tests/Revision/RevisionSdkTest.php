<?php
namespace Api\Sdk\Tests\Revision;

use Api\Sdk\Model\Revision;
use Api\Sdk\Tests\SdkTestCase;

class RevisionSdkTest extends SdkTestCase
{
    public function testGetById()
    {
        $revision = $this->getSdk("revision")->getById(1);

        $this->assertInstanceOf('Api\Sdk\Model\Revision', $revision);
        $this->assertEquals(1, $revision->getContractId());
        $this->assertInstanceOf('Api\Sdk\Model\Contract', $revision->getContract());
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testGetByIdWithBadparameters()
    {
        $this->getSdk("revision")->getById("un");
    }

    public function testGetRevisionForContractByNumber()
    {
        $contract = $this->getSdk("contract")->getById(1);
        $revision = $this->getSdk("revision")->getByContractAndNumber($contract, 2);

        $this->assertInstanceOf('Api\Sdk\Model\Revision', $revision);
        $this->assertEquals(1, $revision->getContractId());
        $this->assertEquals(2, $revision->getNumber());
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testGetRevisionForContractByNumberWithBadparameters()
    {
        $contract = $this->getSdk("contract")->getById(1);
        $this->getSdk("revision")->getByContractAndNumber($contract,"deux");
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testGetContractWithBadParameter()
    {
        $revision = $this->getSdk("revision")->getById(1);
        $revision->setContractId("un");
        $revision->getContract();
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testGetUserWithBadParameter()
    {
        $this->getSdk("revision")->getUser("p_carole");
    }

    public function testGetRevisionsPublished()
    {
        $revisionsPublished = $this->getSdk("revision")->getRevisionsPublished(1);

        foreach ($revisionsPublished as $revisionPublished) {
            $this->assertInstanceOf('Api\Sdk\Model\Revision', $revisionPublished);
            $this->assertEquals(1, $revisionPublished->getContractId());
            $this->assertEquals(Revision::STATUS_PUBLISHED, $revisionPublished->getStatus());
        }
    }
}
