<?php

namespace Senegal\BackBundle\Tests\Entity;

use Senegal\BackBundle\Entity\Contract;
use Senegal\BackBundle\Entity\RapprochementSet;
use Senegal\BackBundle\Entity\Version;
use Senegal\BackBundle\Tests\BaseUnitTestCase;

class RapprochementSetTest extends BaseUnitTestCase
{
    public function testId()
    {
        $rapprochementSet = new RapprochementSet();
        $this->assertNull($rapprochementSet->getId());

        $rapprochementSet->setId(1);
        $this->assertEquals(1, $rapprochementSet->getId());
    }

    public function testClone()
    {
        $rapprochementSet = new RapprochementSet();
        $rapprochementSet->setId(1);

        $version = new Version();
        $rapprochementSet->setVersion($version);

        $rapprochementSetClone = clone $rapprochementSet;
        $this->assertEquals($rapprochementSet->getVersion(), $rapprochementSetClone->getVersion());
        $this->assertNull($rapprochementSetClone->getId());
    }

    public function testTitle()
    {
        $rapprochementSet = new RapprochementSet();
        $this->assertNull($rapprochementSet->getTitle());

        $rapprochementSet->setTitle('My RapprochementSet title');
        $this->assertEquals('My RapprochementSet title', $rapprochementSet->getTitle());
    }

    public function testTitleAssertNotBlankConstraint()
    {
        $rapprochementSet = new RapprochementSet();

        $violationList = $this->getConstraintsValidator()->validateProperty($rapprochementSet, 'title');
        $this->assertEquals(1, $violationList->count());
        $this->assertNull($violationList->get(0)->getInvalidValue());
        $this->assertEquals('rapprochement_set.fields.empty.title', $violationList->get(0)->getMessage());

        $rapprochementSet->setTitle('');
        $this->assertEmpty($rapprochementSet->getTitle());

        $violationList = $this->getConstraintsValidator()->validateProperty($rapprochementSet, 'title');
        $this->assertEquals(1, $violationList->count());
        $this->assertEmpty($violationList->get(0)->getInvalidValue());
        $this->assertEquals('rapprochement_set.fields.empty.title', $violationList->get(0)->getMessage());
    }

    public function testWorkingFormula()
    {
        $rapprochementSet = new RapprochementSet();
        $this->assertNull($rapprochementSet->getWorkingFormula());

        $rapprochementSet->setWorkingFormula('My RapprochementSet working formula');
        $this->assertEquals('My RapprochementSet working formula', $rapprochementSet->getWorkingFormula());
    }

    public function testValidFormula()
    {
        $rapprochementSet = new RapprochementSet();
        $this->assertNull($rapprochementSet->getValidFormula());

        $rapprochementSet->setValidFormula('My RapprochementSet valid formula');
        $this->assertEquals('My RapprochementSet valid formula', $rapprochementSet->getValidFormula());
    }

    public function testCreatedAt()
    {
        $rapprochementSet = new RapprochementSet();
        $this->assertNull($rapprochementSet->getCreatedAt());
    }

    public function testUpdatedAt()
    {
        $rapprochementSet = new RapprochementSet();
        $this->assertNull($rapprochementSet->getUpdatedAt());
    }

    public function testIsWorkingFormulaValid()
    {
        $rapprochementSet = new RapprochementSet();

        $this->assertFalse($rapprochementSet->isWorkingFormulaValid());

        $rapprochementSet->setWorkingFormula('test');
        $this->assertFalse($rapprochementSet->isWorkingFormulaValid());

        $rapprochementSet->setValidFormula('test');
        $this->assertTrue($rapprochementSet->isWorkingFormulaValid());
    }

    public function testPosition()
    {
        $rapprochementSet = new RapprochementSet();

        $this->assertEquals(0, $rapprochementSet->getPosition());

        $rapprochementSet->setPosition(10);
        $this->assertEquals(10, $rapprochementSet->getPosition());
    }

    public function testAllContracts()
    {
        $contract1 = new Contract();
        $contract2 = new Contract();
        $contract3 = new Contract();

        $rapprochementSet = new RapprochementSet();
        $rapprochementSet->setContracts([$contract1, $contract2]);
        $this->assertEquals([$contract1, $contract2], $rapprochementSet->getAllContracts()->toArray());

        $rapprochementSet->addContract($contract3);
        $this->assertEquals([$contract1, $contract2, $contract3], $rapprochementSet->getAllContracts()->toArray());
    }

    public function testContractsAndContractData()
    {
        $version = new Version();

        $contract1 = new Contract();
        $contract1->setId(1);
        $contract2 = new Contract();
        $contract2->setId(2);
        $contract3 = new Contract();
        $contract3->setId(3);
        $contract4 = new Contract();
        $contract4->setId(4);
        $contract5 = new Contract();
        $contract5->setId(5);
        $contract6 = new Contract();
        $contract6->setId(6);
        $contract7 = new Contract();
        $contract7->setId(7);
        $contract8 = new Contract();
        $contract8->setId(8);
        $contract9 = new Contract();
        $contract9->setId(9);
        $contract10 = new Contract();
        $contract10->setId(10);

        $rapprochementSet1 = new RapprochementSet();
        $rapprochementSet1->setId(1);
        $rapprochementSet1->setPosition(0);
        $rapprochementSet2 = new RapprochementSet();
        $rapprochementSet2->setId(2);
        $rapprochementSet2->setPosition(1);
        $rapprochementSet3 = new RapprochementSet();
        $rapprochementSet3->setId(3);
        $rapprochementSet3->setPosition(2);

        $rapprochementSet1->setVersion($version);
        $rapprochementSet2->setVersion($version);
        $rapprochementSet3->setVersion($version);

        $contract1->addRapprochementSet($rapprochementSet1);
        $contract1->addRapprochementSet($rapprochementSet3);

        $contract2->addRapprochementSet($rapprochementSet1);
        $contract2->addRapprochementSet($rapprochementSet3);

        $contract3->addRapprochementSet($rapprochementSet1);
        $contract3->addRapprochementSet($rapprochementSet3);

        $contract4->addRapprochementSet($rapprochementSet1);
        $contract4->addRapprochementSet($rapprochementSet2);
        $contract4->addRapprochementSet($rapprochementSet3);

        $contract5->addRapprochementSet($rapprochementSet1);
        $contract5->addRapprochementSet($rapprochementSet2);
        $contract5->addRapprochementSet($rapprochementSet3);

        $contract6->addRapprochementSet($rapprochementSet2);
        $contract6->addRapprochementSet($rapprochementSet3);

        $contract7->addRapprochementSet($rapprochementSet2);
        $contract7->addRapprochementSet($rapprochementSet3);

        $contract8->addRapprochementSet($rapprochementSet2);
        $contract8->addRapprochementSet($rapprochementSet3);

        $contract9->addRapprochementSet($rapprochementSet3);

        $contract10->addRapprochementSet($rapprochementSet3);

        $rapprochementSet1->setContracts([$contract1, $contract2, $contract3, $contract4, $contract5]);
        $rapprochementSet2->setContracts([$contract4, $contract5, $contract6, $contract7, $contract8]);
        $rapprochementSet3->setContracts([$contract1, $contract2, $contract3, $contract4, $contract5, $contract6, $contract7, $contract8, $contract9, $contract10]);


        $this->assertEquals($rapprochementSet1->getContracts(), [
            'all_contracts' => [$contract1, $contract2, $contract3, $contract4, $contract5],
            'previous_sibling_contracts' => [],
            'contracts' => [$contract1, $contract2, $contract3, $contract4, $contract5],
        ]);

        $this->assertEquals($rapprochementSet2->getContracts(), [
            'all_contracts' => [$contract4, $contract5, $contract6, $contract7, $contract8],
            'previous_sibling_contracts' => [$contract1, $contract2, $contract3, $contract4, $contract5],
            'contracts' => [$contract6, $contract7, $contract8],
        ]);

        $this->assertEquals($rapprochementSet3->getContracts(), [
            'all_contracts' => [$contract1, $contract2, $contract3, $contract4, $contract5, $contract6, $contract7, $contract8, $contract9, $contract10],
            'previous_sibling_contracts' => [$contract4, $contract5, $contract6, $contract7, $contract8, $contract1, $contract2, $contract3],
            'contracts' => [$contract9, $contract10],
        ]);


        $this->assertEquals($rapprochementSet1->getContractData(), [
            'totalContracts' => 5,
            'multiRapprochementSetsContracts' => []
        ]);

        $this->assertEquals($rapprochementSet2->getContractData(), [
            'totalContracts' => 3,
            'multiRapprochementSetsContracts' => [$contract4, $contract5]
        ]);

        $this->assertEquals($rapprochementSet3->getContractData(), [
            'totalContracts' => 2,
            'multiRapprochementSetsContracts' => [$contract1, $contract2, $contract3, $contract4, $contract5, $contract6, $contract7, $contract8]
        ]);
    }
}
