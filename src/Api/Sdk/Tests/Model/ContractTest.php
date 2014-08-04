<?php
namespace Api\Sdk\Tests\Model;

use Api\Sdk\Model\Contract;
use Api\Sdk\Tests\SdkTestCase;
use Api\Sdk\Model\Revision;

/**
 * Class ContractTest
 */
class ContractTest extends SdkTestCase
{

    public function testCreateFromArray()
    {
        $contractData = [
            'id'                => 1,
            'name'              => 'MEDIPART',
            'isActive'          => true,
            'isMarketed'        => false,
            'fullName'          => 'MEDIPART FULL NAME',
            'planName'          => 'FORMULE UNIQUE (DECES + PTIA + ITT + IPT + IPP)',
            'planNumber'        => 1,
            'planTotalNumber'   => 1,
            'productLineId'     => 20,
            'parentId'          => null,
            'hasParent'         => false,
            'isParent'          => true,
            'releasedAt'        => new \DateTime(),
            'inheritsDocuments' => true,

        ];

        // Create a fake contract for testing
        $contract = new Contract($this->getSdk("contract"), $contractData);

        // Assert
        $this->assertEquals($contract->getId(), $contractData['id']);
        $this->assertEquals($contract->getName(), $contractData['name']);
        $this->assertEquals($contract->isActive(), $contractData['isActive']);
        $this->assertEquals($contract->isMarketed(), $contractData['isMarketed']);
        $this->assertEquals($contract->getPlanName(), $contractData['planName']);
        $this->assertEquals($contract->getPlanNumber(), $contractData['planNumber']);
        $this->assertEquals($contract->getPlanTotalNumber(), $contractData['planTotalNumber']);
        $this->assertEquals($contract->getProductLineId(), $contractData['productLineId']);
        $this->assertEquals($contract->getParentId(), $contractData['parentId']);
        $this->assertEquals($contract->hasParent(), $contractData['hasParent']);
        $this->assertEquals($contract->isParent(), $contractData['isParent']);
        $this->assertEquals($contract->getReleasedAt(), $contractData['releasedAt']);
        $this->assertEquals($contract->getInheritanceStatusName(), "Père");
        $this->assertEquals($contract->__toString(), $contract->getFullName());
        $this->assertEquals($contract->inheritsDocuments(), $contractData['inheritsDocuments']);
    }

    public function testInvalidArgument()
    {
        $contract  = new Contract($this->getSdk("contract"));
        $this->setExpectedException('InvalidArgumentException');
        $contract->setSiblings(array(null));
    }

    /**
     * @dataProvider getMethodNames
     */
    public function testValidArgument($method, $data)
    {
        $contract   = new Contract($this->getSdk("contract"));

        $contract->$method(array($data));
        $this->assertTrue(true, 'No exception was thrown');
    }

    public function getMethodNames()
    {
        return [
            ['setSiblings', new Contract($this->getSdk("contract"))],
            ['setDistributors', array('id' => 1, 'name' => 'distributor_name')],
        ];
    }

    public function testGetFullnameWithFormulaInNotFutureContract()
    {
        $contract   = $this->getSdk("contract")->getById(25);

        $fullname = $contract->getName() . " - " . $contract->getPlanName() . " - " . $contract->getPlanNumber() . "/" . $contract->getPlanTotalNumber();

        $this->assertSame($fullname, $contract->getFullName());
    }

    public function testGetFullnameWithFormulaInFutureContract()
    {
        $current = $this->getSdk("contract")->getById(25);

        $future = $this->getSdk("contract")->getById(1);

        $this->assertSame($future->getFullName(), $current->getFullName());
    }

    public function testGetFullnameWithoutFormula()
    {
        $contract   = $this->getSdk("contract")->getById(5533);

        $constructFullname = $contract->getName();
        $this->assertSame($constructFullname, $contract->getFullName());
    }

    public function testGetProductLine()
    {
        $contract   = $this->getSdk("contract")->getById(1);

        $productLine = $contract->getProductLine();

        $this->assertInstanceOf('Api\Sdk\Model\ProductLine', $productLine);
    }

    public function testGetRevision()
    {
        $contract   = $this->getSdk("contract")->getById(1);
        $revision   = $contract->getRevision(1);

        $this->assertInstanceOf('Api\Sdk\Model\Revision', $revision);
        $this->assertEquals(1, $revision->getContractId());
        $this->assertEquals(1, $revision->getNumber());
    }

    public function testGetRevisions()
    {
        $contract   = $this->getSdk("contract")->getById(1);
        $revisions  = $contract->getRevisions();

        foreach ($revisions as $revision) {
            $this->assertInstanceOf('Api\Sdk\Model\Revision', $revision);
            $this->assertEquals(1, $revision->getContractId());
        }
    }

    /**
     * @dataProvider revisionStatusProvider
     *
     * @param $contractId
     * @param $status
     * @param $expected
     */
    public function testGetRevisionWithStatus($contractId, $status, $expected)
    {
        $contract = $this->getSdk("contract")->getById($contractId);

        $this->assertEquals($expected, (boolean) $contract->getRevisionWithStatus($status));
    }

    public function revisionStatusProvider()
    {
        return array(
            array(1, Revision::STATUS_IN_PROGRESS, true),
            array(1, Revision::STATUS_PUBLISHED, true),
            array(1, Revision::STATUS_ARCHIVED, true ),
            array(5533, Revision::STATUS_IN_PROGRESS, false ),
            array(5533, Revision::STATUS_PUBLISHED, false ),
            array(5533, Revision::STATUS_ARCHIVED, false ),
        );
    }

    /**
     * @param $contractId
     * @param $expected
     *
     * @dataProvider canHaveNewRevisionProvider
     */
    public function testCanHaveNewRevision($contractId, $expected)
    {
        $contract   = $this->getSdk("contract")->getById($contractId);
        $this->assertEquals($expected, $contract->canHaveNewRevision());
    }

    public static function canHaveNewRevisionProvider()
    {
        return array(
            array(1, false),
            array(5533, true),
        );
    }

    /**
     * @param $contractId
     * @param $expected
     *
     * @dataProvider getPublishRevisionProvider
     */
    public function testGetPublishedRevision($contractId, $expected)
    {
        $contract   = $this->getSdk("contract")->getById($contractId);
        $this->assertEquals($expected, (boolean) $contract->getPublishedRevision());
    }

    public static function getPublishRevisionProvider()
    {
        return array(
            array(1, true),
            array(5533, false),
        );
    }

    /**
     * @param $contractId
     * @param $expected
     *
     * @dataProvider getRevisionInProgressProvider
     */
    public function testGetRevisionInProgress($contractId, $expected)
    {
        $contract   = $this->getSdk("contract")->getById($contractId);
        $this->assertEquals($expected, (boolean) $contract->getRevisionInProgress());
    }

    public static function getRevisionInProgressProvider()
    {
        return array(
            array(1, true),
            array(5533, false),
        );
    }

    /**
     * @param $contractId
     * @param $expected
     *
     * @dataProvider hasRevisionsProvider
     */
    public function testHasRevisions($contractId, $expected)
    {
        $contract   = $this->getSdk("contract")->getById($contractId);
        $this->assertEquals($expected, $contract->hasRevisions());
    }

    public static function hasRevisionsProvider()
    {
        return array(
            array(1, true),
            array(5533, false),
        );
    }

    /**
     * Test if contract #1 has exactly 2 documents
     */
    public function testGetDocuments()
    {
        $contract   = $this->getSdk("contract")->getById(1);
        $documents  = $contract->getDocuments();

        $this->assertEquals(count($documents), 2);

        array_walk($documents,  function ($document) {
          $this->assertInstanceOf('Api\Sdk\Model\Document', $document);
        });
    }

    /**
     * Format :
     * [[contract_id, source_fields_number], ...]
     *
     * @return array
     */
    public function getFieldSourcesProvider()
    {
        return array(
            array(1, 1),    // contract #1 has a published revision with one source field
            array(5533, 0), // contract #5533 has not revision
            array(404, 0),  // contract #404 has revisions but not published
        );
    }

    /**
     * @dataProvider getFieldSourcesProvider
     */
    public function testGetFieldSources($contractId, $fieldSourcesCount)
    {
        $contract = $this->getSdk("contract")->getById($contractId);
        $fieldSources = $contract->getFieldSources();

        $this->assertThat($fieldSources, new \PHPUnit_Framework_Constraint_IsType('array'));
        $this->assertCount($fieldSourcesCount, $fieldSources);

        // assert that contract field source and published field source are
        if ($fieldSourcesCount > 0) {
            $this->assertEquals($contract->getPublishedRevision()->getFieldSources(), $fieldSources);
        }
    }

    public function testGetOpenedRevision()
    {
        $openedRevision = $this->getSdk("contract")->getById(1)->getOpenedRevision();

        $this->assertInstanceOf('Api\Sdk\Model\Revision', $openedRevision);
        $this->assertEquals(1, $openedRevision->getContractId());
        $this->assertFalse($openedRevision->isPublished());
        $this->assertFalse($openedRevision->isArchived());
    }

    public function testGetCurrent()
    {
        $current = $this->getSdk("contract")->getById(1)->getCurrent();

        $this->assertInstanceOf('Api\Sdk\Model\Contract', $current);
        $this->assertEquals(25, $current->getId());
    }

    public function testGetCurrentWithNotFuture()
    {
        $this->setExpectedException('Api\Sdk\SdkException', 'Contract #25 is not a future contract');
        $this->getSdk("contract")->getById(25)->getCurrent();
    }
}
