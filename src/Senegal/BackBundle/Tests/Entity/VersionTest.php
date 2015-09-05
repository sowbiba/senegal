<?php

namespace Senegal\BackBundle\Tests\Entity;

use Senegal\BackBundle\Entity\Contract;
use Senegal\BackBundle\Entity\ContractSet;
use Senegal\BackBundle\Entity\ContractSetIdentity;
use Senegal\BackBundle\Entity\ContractSetZone;
use Senegal\BackBundle\Entity\RapprochementSet;
use Senegal\BackBundle\Entity\Version;
use Senegal\BackBundle\Entity\VersionHistory;
use Senegal\BackBundle\Entity\Zone;
use Senegal\BackBundle\Tests\BaseUnitTestCase;

class VersionTest extends BaseUnitTestCase
{
    public function testId()
    {
        $version = new Version();
        $this->assertNull($version->getId());

        $version->setId(1);
        $this->assertEquals(1, $version->getId());
    }

    public function testContractSet()
    {
        $version = new Version();
        $this->assertNull($version->getContractSet());

        $version->setContractSet(new ContractSet());
        $this->assertNotNull($version->getContractSet());
    }

    public function testHash()
    {
        $version = new Version();
        $this->assertNotNull($version->getHash());

        $version2 = new Version('test');
        $this->assertEquals('test', $version2->getHash());
    }

    public function testContractSetIdentity()
    {
        $version = new Version();
        $this->assertNull($version->getContractSetIdentity());

        $version->setContractSetIdentity(new ContractSetIdentity());
        $this->assertNotNull($version->getContractSetIdentity());
        $this->assertTrue($version->hasContractSetIdentity());

        $version->setContractSetIdentity(null);
        $this->assertNull($version->getContractSetIdentity());
        $this->assertFalse($version->hasContractSetIdentity());
    }

    public function testContracts()
    {
        $version = new Version();
        $this->assertEmpty($version->getContracts());

        $contract1 = new Contract();
        $contract2 = new Contract();
        $contracts = [$contract1, $contract2];

        $version->setContracts($contracts);
        $this->assertCount(2, $version->getContracts());
        $version->removeContract($contract1);
        $this->assertCount(1, $version->getContracts());
    }

    public function testRapprochementSets()
    {
        $version = new Version();
        $this->assertEmpty($version->getRapprochementSets());

        $rapprochementSet1 = new RapprochementSet();
        $rapprochementSet2 = new RapprochementSet();
        $rapprochementSets = [$rapprochementSet1, $rapprochementSet2];

        $version->setRapprochementSets($rapprochementSets);
        $this->assertCount(2, $version->getRapprochementSets());
        $version->removeRapprochementSet($rapprochementSet1);
        $this->assertCount(1, $version->getRapprochementSets());
    }

    public function testPreviousVersion()
    {
        $version = new Version();
        $this->assertNull($version->getPreviousVersion());

        $version2 = new Version();
        $version->setPreviousVersion($version2);
        $this->assertSame($version2, $version->getPreviousVersion());
    }

    public function testActive()
    {
        $version = new Version();

        $this->assertFalse($version->getActive());
        $this->assertFalse($version->isActive());

        $version->setActive(true);
        $this->assertTrue($version->getActive());
        $this->assertTrue($version->isActive());

        $version->setActive(false);
        $this->assertFalse($version->getActive());
        $this->assertFalse($version->isActive());
    }

    public function testUpdatable()
    {
        $version = new Version();

        $this->assertFalse($version->getUpdatable());
        $this->assertFalse($version->isUpdatable());

        $version->setUpdatable(true);
        $this->assertTrue($version->getUpdatable());
        $this->assertTrue($version->isUpdatable());

        $version->setUpdatable(false);
        $this->assertFalse($version->getUpdatable());
        $this->assertFalse($version->isUpdatable());
    }

    public function testVisible()
    {
        $version = new Version();

        $this->assertFalse($version->getVisible());
        $this->assertFalse($version->isVisible());

        $version->setVisible(true);
        $this->assertTrue($version->getVisible());
        $this->assertTrue($version->isVisible());

        $version->setVisible(false);
        $this->assertFalse($version->getVisible());
        $this->assertFalse($version->isVisible());
    }

    public function testZone()
    {
        $version = new Version();

        $zoneDraft = new Zone();
        $zoneDraft->setId(Zone::DRAFT_ID);

        $zoneDemo = new Zone();
        $zoneDemo->setId(Zone::DEMO_ID);

        $zonePublish = new Zone();
        $zonePublish->setId(Zone::PUBLISH_ID);

        $version->setZone($zoneDraft);
        $this->assertTrue($version->isInDraftZone());

        $version->setZone($zoneDemo);
        $this->assertTrue($version->isInDemoZone());

        $version->setZone($zonePublish);
        $this->assertTrue($version->isInPublishZone());
    }

    public function testCreatedAt()
    {
        $version = new Version();
        $this->assertNull($version->getCreatedAt());
        $date = new \DateTime();
        $version->setCreatedAt($date);
        $this->assertEquals($date, $version->getCreatedAt());
    }

    public function testCreatedBy()
    {
        $version = new Version();
        $this->assertNull($version->getCreatedBy());
    }

    public function testHistorizedAt()
    {
        $version = new Version();
        $this->assertNull($version->getHistorizedAt());
        $date = new \DateTime();
        $version->setHistorizedAt($date);
        $this->assertEquals($date, $version->getHistorizedAt());
    }

    public function testUpdatedAt()
    {
        $version = new Version();
        $this->assertNotNull($version->getUpdatedAt());
        $date = new \DateTime();
        $version->setUpdatedAt($date);
        $this->assertEquals($date, $version->getUpdatedAt());
    }
}
