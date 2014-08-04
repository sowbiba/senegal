<?php
/**
 * Author: Florent Coquel
 * Date: 24/07/13
 */

namespace Api\Sdk\Tests\Revision\Connector\Doctrine;

use Api\Sdk\Model\RevisionFieldSource;
use Api\Sdk\Revision\Connector\Doctrine\RevisionDoctrineConnector;
use Api\Sdk\Model\Contract;
use Api\Sdk\Model\Revision;
use Api\Sdk\Model\Field;
use Api\SdkBundle\Entity\Revision as RevisionEntity;
use Api\SdkBundle\Entity\Contract as ContractEntity;
use Api\Sdk\Tests\SdkTestCase;

class RevisionDoctrineConnectorTest extends SdkTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $em;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $repository;

    /**
     * @var RevisionDoctrineConnector
     */
    private $connector;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $revisionSdk;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $contractSdk;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $logger;

    public function setUp()
    {
        $this->em         = $this->getMockWithoutConstructor('Doctrine\ORM\EntityManager');
        $this->repository = $this->getMockWithoutConstructor('Doctrine\ORM\EntityRepository');
        $this->logger     = $this->getMockWithoutConstructor('Monolog\Logger');

        $this->contractSdk = $this->getMockWithoutConstructor("Api\Sdk\Contract\ContractSdk");
        $this->revisionSdk = $this->getMockWithoutConstructor("Api\Sdk\Revision\RevisionSdk");

        $this->em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($this->repository));

        $this->connector = $this->getMock(
            'Api\Sdk\Revision\Connector\Doctrine\RevisionDoctrineConnector',
            array('getCollection'),
            array($this->em, $this->logger)
        );

        $this->connector->expects($this->any())
            ->method('getCollection')
            ->will($this->returnValue(array()));
    }

    public function testCreate()
    {
        $productLineTestId = 42;

        $tableName = sprintf('revision_value_pl%1$03d', $productLineTestId);

        $revisionValuesSql = <<<EOF
        CREATE TABLE IF NOT EXISTS `$tableName` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `revision_id` int(11) NOT NULL,
        `field_id` int(11) NOT NULL,
        `value` text NOT NULL,
        PRIMARY KEY (`id`),
        KEY (`revision_id`),
        KEY (`field_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ALTER TABLE `$tableName`
            ADD UNIQUE (`revision_id` ,`field_id`);
        ALTER TABLE `$tableName`
            ADD CONSTRAINT FOREIGN KEY (`field_id`)
                REFERENCES `champs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT FOREIGN KEY (`revision_id`)
                REFERENCES `revision` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
EOF;

        $tableName = sprintf('revision_field_source_pl%1$03d', $productLineTestId);

        $fieldSourceSql = <<<EOF
        CREATE TABLE IF NOT EXISTS `$tableName` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `revision_id` int(11) NOT NULL,
        `field_id` int(11) NOT NULL,
        `document_id` int(11) NOT NULL,
        `page_number` int(11) NOT NULL,
        PRIMARY KEY (`id`),
        KEY (`revision_id`),
        KEY (`field_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ALTER TABLE `$tableName`
            ADD UNIQUE (`revision_id` ,`field_id`);
        ALTER TABLE `$tableName`
            ADD CONSTRAINT FOREIGN KEY (`field_id`)
                REFERENCES `champs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT FOREIGN KEY (`revision_id`)
                REFERENCES `revision` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT FOREIGN KEY (`document_id`)
                REFERENCES `documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
EOF;

        $connection     = $this->getMockWithoutConstructor('Doctrine\DBAL\Connection');
        $mediator       = $this->getMockWithoutConstructor('Api\Sdk\Mediator\SdkMediator');
        $productLine    = $this->getMockWithoutConstructor('Api\Sdk\Model\ProductLine');
        $productLineSdk = $this->getMockWithoutConstructor('Api\Sdk\ProductLine\ProductLineSdk');
        $contract       = $this->getMockWithoutConstructor('Api\Sdk\Model\Contract');
        $revision       = $this->getMockWithoutConstructor('Api\Sdk\Model\Revision');

        $schemaManager  = $this->getMockWithoutConstructor('\Doctrine\DBAL\Schema\MySqlSchemaManager');
        $schemaManager->expects($this->any())
            ->method('tableExists')
            ->will($this->returnValue(false));

        $contractEntity    = $this->getMockWithoutConstructor('Api\SdkBundle\Entity\Contract');
        $productLineEntity = $this->getMockWithoutConstructor('Api\SdkBundle\Entity\ProductLine');

        $connection->expects($this->any())
            ->method('getSchemaManager')
            ->will($this->returnValue($schemaManager));

        $connection->expects($this->at(1))
            ->method('exec')
            ->with($revisionValuesSql);

        $connection->expects($this->at(2))
            ->method('exec')
            ->with($fieldSourceSql);

        $productLine->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($productLineTestId));

        $productLineSdk->expects($this->any())
            ->method('getFieldIds')
            ->will($this->returnValue(array()));

        $contract->expects($this->any())
            ->method('getProductLine')
            ->will($this->returnValue($productLine));

        $mediator->expects($this->any())
            ->method('getColleague')
            ->with('productLine')
            ->will($this->returnValue($productLineSdk));

        $revision->expects($this->any())
            ->method('getContract')
            ->will($this->returnValue($contract));

        $this->em->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($connection));

        $contractEntity->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($productLineTestId));

        $productLineEntity->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($productLineTestId));

        $contractEntity->expects($this->any())
            ->method('getProductLine')
            ->will($this->returnValue($productLineEntity));

        $this->em->expects($this->once())
            ->method('find')
            ->with('ApiSdkBundle:Contract', $productLineTestId)
            ->will($this->returnValue($contractEntity));

        $this->connector->setMediator($mediator);

        $result = $this->connector->create(array(
            'contractId' => $productLineTestId,
            'number'     => 'toto',
        ));

        $this->assertSame($productLineTestId, $result['contractId']);
        $this->assertSame('toto', $result['number']);
    }

    public function testGetRevision()
    {
        $contractEntity = new ContractEntity();
        $contractEntity->setId(404);

        $revisionEntity = new RevisionEntity();
        $revisionEntity->setContract($contractEntity);
        $revisionEntity->setId(2);
        $revisionEntity->setStatus(Revision::STATUS_IN_PROGRESS);

        $this->repository->expects($this->once())
            ->method("find")
            ->will($this->returnValue($revisionEntity));

        $revision = $this->connector->getById(1);

        $this->assertEquals($revision['number'], 1);
        $this->assertEquals($revision['status'], Revision::STATUS_IN_PROGRESS);
        $this->assertInstanceOf("DateTime", $revision['createdAt']);
        $this->assertInstanceOf("DateTime", $revision['updatedAt']);
    }

    public function testGetInvalideRevision()
    {
        $this->repository->expects($this->once())
            ->method("find")
            ->will($this->returnValue(array()));

        $revision = $this->connector->getById(1);

        $this->assertNull($revision);
    }

    public function testGetRevisionForContractWithNumber()
    {
        $contract = new Contract($this->contractSdk);
        $contract->setId(404);

        $contractEntity = new ContractEntity();
        $contractEntity->setId(404);

        $revisionEntity = new RevisionEntity();
        $revisionEntity->setContract($contractEntity);
        $revisionEntity->setId(1);
        $revisionEntity->setStatus(Revision::STATUS_IN_PROGRESS);

        $this->repository->expects($this->once())
            ->method("findOneBy")
            ->will($this->returnValue($revisionEntity));

        $revision = $this->connector->getRevisionForContractWithNumber($contract, 1);

        $this->assertEquals($revision['number'], 1);
        $this->assertEquals($revision['status'], Revision::STATUS_IN_PROGRESS);
        $this->assertEquals($revision['contractId'], 404);
        $this->assertInstanceOf("DateTime", $revision['createdAt']);
        $this->assertInstanceOf("DateTime", $revision['updatedAt']);
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testGetInvalidRevisionForContractWithNumber()
    {
        $contract = new Contract($this->contractSdk);
        $revision = $this->connector->getRevisionForContractWithNumber($contract, "prout");

        $this->assertFalse($revision);

    }

    public function testDeleteRevision()
    {
        $contractEntity = new ContractEntity();
        $contractEntity->setId(404);

        $revisionEntity = new RevisionEntity();
        $revisionEntity->setContract($contractEntity);
        $revisionEntity->setId(2);
        $revisionEntity->setStatus(Revision::STATUS_IN_PROGRESS);

        $this->repository->expects($this->once())
            ->method("find")
            ->will($this->returnValue($revisionEntity));

        $result = $this->connector->delete(new Revision($this->revisionSdk, $revisionEntity->toArray()));

        $this->assertTrue($result);
    }

    public function testDeleteInvalidRevision()
    {
        $contractEntity = new ContractEntity();
        $contractEntity->setId(404);

        $revisionEntity = new RevisionEntity();
        $revisionEntity->setContract($contractEntity);
        $revisionEntity->setId(2);
        $revisionEntity->setStatus(Revision::STATUS_PUBLISHED);

        $result = $this->connector->delete(new Revision($this->revisionSdk, $revisionEntity->toArray()));

        $this->assertFalse($result);
    }

    public function testSubmitRevisionSuccess()
    {
        $contractEntity = new ContractEntity();
        $contractEntity->setId(404);

        $revisionEntity = new RevisionEntity();
        $revisionEntity->setContract($contractEntity);
        $revisionEntity->setId(2);
        $revisionEntity->setStatus(Revision::STATUS_IN_PROGRESS);

        $this->repository->expects($this->once())
            ->method("find")
            ->will($this->returnValue($revisionEntity));

        $contract = new Contract($this->contractSdk);
        $contract->setId(404);

        $revision = $this->getMockBuilder('Api\Sdk\Model\Revision')
            ->disableOriginalConstructor()
            ->getMock();

        $revision->expects($this->any())
            ->method('canBeSubmitted')
            ->will(($this->returnValue(true)));

        $result = $this->connector->submitRevision($revision);

        $this->assertTrue($result);
    }

    public function testSubmitRevisionFail()
    {
        $revision = $this->getMockBuilder('Api\Sdk\Model\Revision')
            ->disableOriginalConstructor()
            ->getMock();

        $revision->expects($this->any())
            ->method('canBeSubmitted')
            ->will(($this->returnValue(false)));

        $result = $this->connector->submitRevision($revision);

        $this->assertFalse($result);
    }

    public function testRejectRevisionSuccess()
    {
        $contractEntity = new ContractEntity();
        $contractEntity->setId(404);

        $revisionEntity = new RevisionEntity();
        $revisionEntity->setContract($contractEntity);
        $revisionEntity->setId(2);
        $revisionEntity->setStatus(Revision::STATUS_SUBMITTED);

        $this->repository->expects($this->once())
            ->method("find")
            ->will($this->returnValue($revisionEntity));

        $contract = new Contract($this->contractSdk);
        $contract->setId(404);

        $revision = $this->getMockBuilder('Api\Sdk\Model\Revision')
            ->disableOriginalConstructor()
            ->getMock();

        $revision->expects($this->any())
            ->method('canBeRejected')
            ->will(($this->returnValue(true)));

        $result = $this->connector->rejectRevision($revision);

        $this->assertTrue($result);
    }

    public function testRejectRevisionFail()
    {
        $revision = $this->getMockBuilder('Api\Sdk\Model\Revision')
            ->disableOriginalConstructor()
            ->getMock();

        $revision->expects($this->any())
            ->method('canBeRejected')
            ->will(($this->returnValue(false)));

        $result = $this->connector->rejectRevision($revision);

        $this->assertFalse($result);
    }

    public function testPublishRevision()
    {
        $contractEntity = new ContractEntity();
        $contractEntity->setId(404);

        $revisionEntity = new RevisionEntity();
        $revisionEntity->setContract($contractEntity);
        $revisionEntity->setId(2);
        $revisionEntity->setStatus(Revision::STATUS_IN_PROGRESS);

        $this->repository->expects($this->once())
            ->method("find")
            ->will($this->returnValue($revisionEntity));

        $this->repository->expects($this->once())
            ->method("findOneBy")
            ->will($this->returnValue(array()));

        $contract = new Contract($this->contractSdk);
        $contract->setId(404);

        $revision = $this->getMockBuilder('Api\Sdk\Model\Revision')
            ->disableOriginalConstructor()
            ->getMock();

        $revision->expects($this->once())
            ->method('getContract')
            ->will($this->returnValue($contract));

        $revision->expects($this->any())
            ->method('canBePublished')
            ->will(($this->returnValue(true)));

        $revision->expects($this->once())
            ->method('getPreviousNumber')
            ->will($this->returnValue(1));

        $result = $this->connector->publishRevision($revision);

        $this->assertTrue($result);
    }

    public function testPublishRevisionWithPreviousRevision()
    {
        $contract = new Contract($this->contractSdk);
        $contract->setId(404);

        $this->revisionSdk->expects($this->exactly(2))
            ->method('getContract')
            ->will($this->returnValue($contract));

        $this->contractSdk->expects($this->once())
            ->method('getChildren')
            ->will($this->returnValue(array()));

        $contractEntity = new ContractEntity();
        $contractEntity->setId(404);

        $revisionEntity = new RevisionEntity();
        $revisionEntity->setContract($contractEntity);
        $revisionEntity->setId(2);
        $revisionEntity->setStatus(Revision::STATUS_PENDING_PUBLICATION);

        $oldRevisionEntity = new RevisionEntity();
        $oldRevisionEntity->setContract($contractEntity);
        $oldRevisionEntity->setId(1);
        $oldRevisionEntity->setStatus(Revision::STATUS_PUBLISHED);

        $this->repository->expects($this->once())
            ->method("find")
            ->will($this->returnValue($revisionEntity));

        $this->repository->expects($this->once())
            ->method("findOneBy")
            ->will($this->returnValue($oldRevisionEntity));

        $result = $this->connector->publishRevision(new Revision($this->revisionSdk, $revisionEntity->toArray()));

        $this->assertTrue($result);
        $this->assertEquals($oldRevisionEntity->getStatus(), Revision::STATUS_ARCHIVED);
    }

    public function testPublishInvalidRevision()
    {
        $contractEntity = new ContractEntity();
        $contractEntity->setId(404);

        $revisionEntity = new RevisionEntity();
        $revisionEntity->setContract($contractEntity);
        $revisionEntity->setId(2);
        $revisionEntity->setStatus(Revision::STATUS_ARCHIVED);

        $result = $this->connector->publishRevision(new Revision($this->revisionSdk, $revisionEntity->toArray()));

        $this->assertFalse($result);
    }

    public function testUnpublishRevision()
    {
        $contractEntity = new ContractEntity();
        $contractEntity->setId(404);

        $contract = new Contract($this->contractSdk);
        $contract->setId(404);

        $this->revisionSdk->expects($this->any())
            ->method('getContract')
            ->will($this->returnValue($contract));

        $revisionEntity = new RevisionEntity();
        $revisionEntity->setContract($contractEntity);
        $revisionEntity->setId(2);
        $revisionEntity->setStatus(Revision::STATUS_PUBLISHED);

        $this->repository->expects($this->any())
            ->method("find")
            ->will($this->returnValue($revisionEntity));

        $this->repository->expects($this->exactly(2))
            ->method("findOneBy")
            ->will($this->onConsecutiveCalls(array(), array()));

        $revision = new Revision($this->revisionSdk, $revisionEntity->toArray());

        $this->assertTrue($this->connector->unpublishRevision($revision));
    }

    public function testUnpublishRevisionWithPreviousRevision()
    {
        $contract = new Contract($this->contractSdk);
        $contract->setId(404);

        $contractEntity = new ContractEntity();
        $contractEntity->setId(404);

        $this->revisionSdk->expects($this->any())
            ->method('getContract')
            ->will($this->returnValue($contract));

        $revisionEntity = new RevisionEntity();
        $revisionEntity->setContract($contractEntity);
        $revisionEntity->setId(2);
        $revisionEntity->setStatus(Revision::STATUS_PUBLISHED);

        $oldRevisionEntity = new RevisionEntity();
        $oldRevisionEntity->setContract($contractEntity);
        $oldRevisionEntity->setId(1);
        $oldRevisionEntity->setStatus(Revision::STATUS_ARCHIVED);

        $this->repository->expects($this->once())
            ->method("find")
            ->will($this->returnValue($revisionEntity));

        $this->repository->expects($this->exactly(2))
            ->method("findOneBy")
            ->will($this->onConsecutiveCalls($oldRevisionEntity, array()));

        $mockRevision                   = new Revision($this->revisionSdk, $revisionEntity->toArray());
        $mockRevision->canBeUnpublished = true;

        $result = $this->connector->unpublishRevision($mockRevision);

        $this->assertTrue($result);
        $this->assertEquals($oldRevisionEntity->getStatus(), Revision::STATUS_PUBLISHED);
    }

    public function testUnpublishRevisionWithNextRevision()
    {
        $contractEntity = new ContractEntity();
        $contractEntity->setId(404);

        $contract = new Contract($this->contractSdk);
        $contract->setId(404);

        $this->revisionSdk->expects($this->any())
            ->method('getContract')
            ->will($this->returnValue($contract));

        $revisionEntity = new RevisionEntity();
        $revisionEntity->setContract($contractEntity);
        $revisionEntity->setId(2);
        $revisionEntity->setStatus(Revision::STATUS_PUBLISHED);

        $nextRevisionEntity = new RevisionEntity();
        $nextRevisionEntity->setContract($contractEntity);
        $nextRevisionEntity->setId(3);
        $nextRevisionEntity->setStatus(Revision::STATUS_IN_PROGRESS);

        $this->repository->expects($this->exactly(2))
            ->method("findOneBy")
            ->will($this->onConsecutiveCalls(array(), $nextRevisionEntity));

        $mockRevision                   = new Revision($this->revisionSdk, $revisionEntity->toArray());
        $mockRevision->canBeUnpublished = true;

        $result = $this->connector->unpublishRevision($mockRevision);

        $this->assertFalse($result);
    }

    public function testUnpublishInvalidRevision()
    {
        $revision = $this->getMockBuilder('Api\Sdk\Model\Revision')
            ->disableOriginalConstructor()
            ->getMock();

        $revision->expects($this->once())
            ->method('canBeUnpublished')
            ->will($this->returnValue(false));

        $this->assertFalse($this->connector->unpublishRevision($revision));
    }

    public function dataUpdateValues()
    {
        return array(
            [Field::TYPE_TEXT, [42 => 'champ text', 200 => '#NA'], 'champ text'],
            [Field::TYPE_TEXT, [42 => "Aujourd'hui", 200 => '#NA'], "Aujourd'hui"],
            [Field::TYPE_NUMERIC, [42 => 1, 200 => '#NA'], '1'],
            [Field::TYPE_NUMERIC, [42 => 1.55, 200 => '#NA'], '1.55'],
            [Field::TYPE_NUMERIC, [42 => "206,73", 200 => '#NA'], '206,73'],
            [Field::TYPE_NUMERIC, [42 => "99999999998.999999999", 200 => '#NA'], '99999999998,999999999'],
            [Field::TYPE_NUMERIC, [42 => "42.56000", 200 => '#NA'], '42,56'],
            [Field::TYPE_NUMERIC, [42 => "42000", 200 => '#NA'], '42000'],
            [Field::TYPE_NUMERIC, [42 => "99999999999,999999999", 200 => '#NA'], '99999999999,999999999'],
            [Field::TYPE_LIST, [42 => 3, 200 => '#NA'], '3'],
            [Field::TYPE_LIST, [42 => '5', 200 => '#NA'], '5'],
            [Field::TYPE_DATE, [42 => '21/06/2012', 200 => '#NA'], '2012-06-21'],
            [Field::TYPE_TEXT, [42 => '', 200 => '#NA'], Field::VALUE_NC],
            [Field::TYPE_TEXT, [42 => null, 200 => '#NA'], Field::VALUE_NC],
            [Field::TYPE_TEXT, [42 => Field::VALUE_NC, 200 => '#NA'], Field::VALUE_NC],
        );
    }

    /**
     * Test revision values updated
     *
     * @dataProvider dataUpdateValues
     */
    public function testUpdateValues($fieldTypeId, $data, $serializedValue)
    {
        $objectId = 42;

        // Mock Connection
        $connMock = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        // Mock execute DELETE query
        $connMock->expects($this->at(0))
            ->method('exec')
            ->with("DELETE FROM revision_value_pl0$objectId\nWHERE revision_id = $objectId\nAND field_id IN (200)");

        // Mock execute REPLACE query
        $serializedValue = addslashes($serializedValue);
        $connMock->expects($this->at(2))
            ->method('exec')
            ->with(sprintf("INSERT INTO revision_value_pl0%d (revision_id, field_id, value) VALUES (%d, %d, '%s')
ON DUPLICATE KEY UPDATE
revision_id = VALUES(revision_id),
field_id    = VALUES(field_id),
value       = VALUES(value)", $objectId, $objectId, $objectId, $serializedValue));

        // Mock method getConnection for entityManager with $connMock
        $this->em->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($connMock));

        // Mock model ProductLine
        $productLineMock = $this->getMockBuilder('Api\Sdk\Model\ProductLine')
            ->disableOriginalConstructor()
            ->getMock();

        // Mock method getFieldIds for ProductLine
        $productLineMock->expects($this->any())
            ->method('getFieldIds')
            ->will($this->returnValue(array_keys($data)));

        // Mock method getProductLine for sdk with $productLine
        $this->contractSdk->expects($this->any())
            ->method('getProductLine')
            ->will($this->returnValue($productLineMock));

        $contract = new Contract($this->contractSdk, array('id' => $objectId, 'productLineId' => $objectId));
        // Mock method getContract for sdk with $contract
        $this->revisionSdk->expects($this->any())
            ->method('getContract')
            ->will($this->returnValue($contract));

        // Mock model Field
        $fieldMock = $this->getMockBuilder('Api\Sdk\Model\Field')
            ->disableOriginalConstructor()
            ->getMock();
        // Mock method getTypeId for $fieldMock with $fieldTypeId
        $fieldMock->expects($this->any())
            ->method('getTypeId')
            ->will($this->returnValue($fieldTypeId));
        // Mock method getField for revisionSdk with $fieldMock
        $this->revisionSdk->expects($this->any())
            ->method('getField')
            ->will($this->returnValue($fieldMock));
        // Mock method getFieldSource for revisionSdk with $revisionFieldSource
        $revisionFieldSource = new RevisionFieldSource($this->getSdk('revision'));
        $this->revisionSdk->expects($this->any())
            ->method('getFieldSource')
            ->will($this->returnValue($revisionFieldSource));

        if (Field::TYPE_LIST == $fieldTypeId) {
            // Mock method getListId for $fieldMock with $objectId
            $fieldMock->expects($this->any())
                ->method('getListId')
                ->will($this->returnValue($objectId));
            $fieldMock->expects($this->any())
                ->method('getChoices')
                ->will($this->returnValue(array(3 => 'option 3', 5 => 'option 5')));
        }

        // Create revision
        $revision = new Revision($this->revisionSdk, array('id' => $objectId));
        $entity = $this->getMock('\Api\SdkBundle\Entity\Revision');

        $entity->expects($this->once())
            ->method('populate')
            ->with(array(
                'id' => 42,
                'number' => null,
                'status' => null,
                'contractId' => null,
                'createdAt' => null,
                'updatedAt' => null,
                'publishedAt' => null,
                'createdBy' => null,
                'updatedBy' => null,
                'publishedBy' => null,
                'isCloned' => null,
                'isLocked' => null,
            ));

        $this->repository->expects($this->once())
            ->method('find')
            ->with($objectId)
            ->will($this->returnValue($entity));

        // Update revision's values
        $revisionConnector = new RevisionDoctrineConnector($this->em);
        $revisionConnector->setLogger($this->logger);
        $revisionConnector->setRepository('revision');

        $return = $revisionConnector->updateValues($revision, $data);

        $this->assertTrue($return);
    }

    /**
     * Test revision values not updated, an exception launch but can't load field
     *
     */
    public function testUpdateValuesFailed()
    {
        $objectId = 42;

        // Mock model ProductLine
        $productLineMock = $this->getMockBuilder('Api\Sdk\Model\ProductLine')
            ->disableOriginalConstructor()
            ->getMock();

        // Mock method getFieldIds for ProductLine
        $productLineMock->expects($this->any())
            ->method('getFieldIds')
            ->will($this->returnValue(array(null)));

        // Mock method getProductLine for sdk with $productLine
        $this->contractSdk->expects($this->any())
            ->method('getProductLine')
            ->will($this->returnValue($productLineMock));

        $contract = new Contract($this->contractSdk, array('id' => $objectId, 'productLineId' => $objectId));
        // Mock method getContract for sdk with $contract
        $this->revisionSdk->expects($this->any())
            ->method('getContract')
            ->will($this->returnValue($contract));

        // Mock method getField for revisionSdk with $fieldMock
        $this->revisionSdk->expects($this->any())
            ->method('getField')
            ->will($this->returnValue(null));

        // Create revision
        $revision = new Revision($this->revisionSdk, array('id' => $objectId));

        // Update revision's values
        $revisionConnector = new RevisionDoctrineConnector($this->em);
        $revisionConnector->setLogger($this->logger);

        $result = $revisionConnector->updateValues($revision, array(null => 'champ inexistant'));

        $this->assertFalse($result);
    }

    public function dataGetValues()
    {
        return array(
            [[['field_id' => 42, 'value' => 'champ text', 'type_id' => Field::TYPE_TEXT]], [42 => 'champ text', 200 => Field::VALUE_NA]],
            [[['field_id' => 42, 'value' => '1000,050000000', 'type_id' => Field::TYPE_NUMERIC]], [42 => "1000.05", 200 => Field::VALUE_NA, 300 => Field::VALUE_NA]],
            [[['field_id' => 42, 'value' => '206,730000000', 'type_id' => Field::TYPE_NUMERIC]], [42 => "206.73"]],
            [[['field_id' => 42, 'value' => '5', 'type_id' => Field::TYPE_LIST]], [42 => 5]],
            [[['field_id' => 42, 'value' => '2012-06-21', 'type_id' => Field::TYPE_DATE]], [42 => '21/06/2012']],
            [[['field_id' => 42, 'value' => Field::VALUE_NC, 'type_id' => Field::TYPE_TEXT]], [42 => '']],
        );
    }

    /**
     * Test revision values returned
     *
     * @dataProvider dataGetValues
     */
    public function testGetValues($dataResultSet, $dataUnserialised)
    {
        $objectId = 42;

        // Mock Connection
        $connMock = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        // Mock statement for execute query
        $stmtMock = $this->getMockBuilder('Doctrine\DBAL\Statement')
            ->disableOriginalConstructor()
            ->getMock();

        // Mock method getConnection for entityManager with $connMock
        $this->em->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($connMock));

        // Mock method executeQuery for $connMock with $stmtMock
        $connMock->expects($this->any())
            ->method('executeQuery')
            ->will($this->returnValue($stmtMock));

        // Mock method fetchAll for $stmtMock with $dataResultSet
        $stmtMock->expects($this->any())
            ->method('fetchAll')
            ->will($this->returnValue($dataResultSet));

        // Mock model ProductLine
        $productLineMock = $this->getMockBuilder('Api\Sdk\Model\ProductLine')
            ->disableOriginalConstructor()
            ->getMock();

        // Mock method getFieldIds for ProductLine
        $productLineMock->expects($this->any())
            ->method('getFieldIds')
            ->will($this->returnValue(array_keys($dataUnserialised)));

        // Mock method getProductLine for sdk with $productLine
        $this->contractSdk->expects($this->any())
            ->method('getProductLine')
            ->will($this->returnValue($productLineMock));

        $contract = new Contract($this->contractSdk, array('id' => $objectId, 'productLineId' => $objectId));
        // Mock method getContract for sdk with $contract
        $this->revisionSdk->expects($this->any())
            ->method('getContract')
            ->will($this->returnValue($contract));

        // Mock entity Field
        $fieldMock = $this->getMockBuilder('Api\Sdk\Model\Field')
            ->disableOriginalConstructor()
            ->getMock();

        // Mock method getField for revisionSdk with $fieldMock
        $this->revisionSdk->expects($this->any())
            ->method('getField')
            ->will($this->returnValue($fieldMock));

        // Create revision
        $revision = new Revision($this->revisionSdk, array('id' => $objectId));

        // Return revision's values
        $revisionConnector = new RevisionDoctrineConnector($this->em);
        $revisionConnector->setLogger($this->logger);

        $result = $revisionConnector->getValues($revision);

        $this->assertSame($dataUnserialised, $result);
    }

    public function dataUnserialiseValuesFailed()
    {
        return array(
            [Field::TYPE_COMPUTED, [['field_id' => 42, 'value' => 'type non supporté']]],
            [Field::TYPE_MULTISELECT, [['field_id' => 42, 'value' => 'type non supporté']]],
            [null, [['field_id' => 42, 'value' => 'type non supporté']]],
        );
    }

    /**
     * Test exceptions launch but serialise value failed
     *
     * @dataProvider dataUnserialiseValuesFailed
     */
    public function testUnserialiseValuesFailed($fieldTypeId, $dataResultSet)
    {
        $objectId = 42;

        // Mock Connection
        $connMock = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        // Mock statement for execute query
        $stmtMock = $this->getMockBuilder('Doctrine\DBAL\Statement')
            ->disableOriginalConstructor()
            ->getMock();

        // Mock method getConnection for entityManager with $connMock
        $this->em->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($connMock));

        // Mock method executeQuery for $connMock with $stmtMock
        $connMock->expects($this->any())
            ->method('executeQuery')
            ->will($this->returnValue($stmtMock));

        // Mock method fetchAll for $stmtMock with $dataResultSet
        $stmtMock->expects($this->any())
            ->method('fetchAll')
            ->will($this->returnValue($dataResultSet));

        $contract = new Contract($this->contractSdk, array('id' => $objectId, 'productLineId' => $objectId));
        // Mock method getContract for sdk with $contract
        $this->revisionSdk->expects($this->any())
            ->method('getContract')
            ->will($this->returnValue($contract));

        // Mock entity Field
        $fieldMock = $this->getMockBuilder('Api\Sdk\Model\Field')
            ->disableOriginalConstructor()
            ->getMock();
        // Mock method getTypeId for $fieldMock with $fieldTypeId
        $fieldMock->expects($this->any())
            ->method('getTypeId')
            ->will($this->returnValue($fieldTypeId));

        // Mock method getField for revisionSdk with $fieldMock
        $this->revisionSdk->expects($this->any())
            ->method('getField')
            ->will($this->returnValue($fieldMock));

        // Create revision
        $revision = new Revision($this->revisionSdk, array('id' => $objectId));

        // Return revision's values
        $revisionConnector = new RevisionDoctrineConnector($this->em);
        $revisionConnector->setLogger($this->logger);

        $result = $revisionConnector->getValues($revision);

        $this->assertFalse($result);
    }

    /**
     * Test revision values not returned, an exception launch but can't load field
     *
     */
    public function testGetValuesFailed()
    {
        $objectId = 42;

        // Mock Connection
        $connMock = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        // Mock statement for execute query
        $stmtMock = $this->getMockBuilder('Doctrine\DBAL\Statement')
            ->disableOriginalConstructor()
            ->getMock();

        // Mock method getConnection for entityManager with $connMock
        $this->em->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($connMock));

        // Mock method executeQuery for $connMock with $stmtMock
        $connMock->expects($this->any())
            ->method('executeQuery')
            ->will($this->returnValue($stmtMock));

        // Mock method fetchAll for $stmtMock with $dataResultSet
        $dataResultSet = array(['field_id' => null, 'value' => 'champ inexistant']);
        $stmtMock->expects($this->any())
            ->method('fetchAll')
            ->will($this->returnValue($dataResultSet));

        $contract = new Contract($this->contractSdk, array('id' => $objectId, 'productLineId' => $objectId));
        // Mock method getContract for sdk with $contract
        $this->revisionSdk->expects($this->any())
            ->method('getContract')
            ->will($this->returnValue($contract));

        // Mock method getField for revisionSdk with $fieldMock
        $this->revisionSdk->expects($this->any())
            ->method('getField')
            ->will($this->returnValue(null));

        // Create revision
        $revision = new Revision($this->revisionSdk, array('id' => $objectId));

        // Return revision's values
        $revisionConnector = new RevisionDoctrineConnector($this->em);
        $revisionConnector->setLogger($this->logger);

        $result = $revisionConnector->getValues($revision);

        $this->assertFalse($result);
    }

    /**
     * @return array
     */
    public function dataUpdateFieldSource()
    {
        return array(
            array(
                array('id' => 1, 'revisionId' => 1, 'fieldId' => 1, 'documentId' => 1, 'page' => 15), true,
                array('id' => 1, 'revisionId' => null, 'fieldId' => 1, 'documentId' => 1, 'page' => 15), false,
                array('id' => 1, 'revisionId' => 1, 'fieldId' => null, 'documentId' => 1, 'page' => 15), false,
                array('id' => 1, 'revisionId' => 1, 'fieldId' => 1, 'documentId' => null, 'page' => null), true,
            )
        );
    }

    /**
     * @dataProvider dataUpdateFieldSource
     */
    public function testUpdateFieldSource($fieldSourceData, $expected)
    {
        $revisionFieldSource = new RevisionFieldSource($this->revisionSdk, $fieldSourceData);

        $tableName = sprintf('revision_field_source_pl%1$03d', 42);

        $fieldSourceSql = sprintf(
            'UPDATE %s SET document_id = %d, `page_number` = %d WHERE id = %d',
            $tableName,
            $revisionFieldSource->getDocumentId(),
            $revisionFieldSource->getPage(),
            $revisionFieldSource->getId()
        );

        // Mock Connection
        $connMock = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $mediator          = $this->getMockWithoutConstructor('Api\Sdk\Mediator\SdkMediator');
        $revisionEntity    = $this->getMockWithoutConstructor('Api\SdkBundle\Entity\Revision');
        $contractEntity    = $this->getMockWithoutConstructor('Api\SdkBundle\Entity\Contract');
        $productLineEntity = $this->getMockWithoutConstructor('Api\SdkBundle\Entity\ProductLine');

        $this->repository->expects($this->once())
            ->method('find')
            ->with($fieldSourceData['revisionId'])
            ->will($this->returnValue($revisionEntity));

        $revisionEntity->expects($this->once())
            ->method('getContract')
            ->will($this->returnValue($contractEntity));

        $contractEntity->expects($this->once())
            ->method("getProductLine")
            ->will($this->returnValue($productLineEntity));

        $productLineEntity->expects($this->once())
            ->method('getId')
            ->will($this->returnValue("42"));

        if (true === $expected) {
            $connMock->expects($this->once())
                ->method('exec')
                ->with($fieldSourceSql);
        }

        // Mock method getConnection for entityManager with $connMock
        $this->em->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($connMock));

        // Update revision's values
        $revisionConnector = new RevisionDoctrineConnector($this->em);
        $revisionConnector->setLogger($this->logger);
        $revisionConnector->setMediator($mediator);
        $revisionConnector->updateFieldSource($revisionFieldSource);
    }

    public function testGetRevisionsPublished()
    {
        $revisionsPublished = array('id' => 1, 'contractId' => '99', 'hasPublished' => true);

        $connector = $this->getMock(
            'Api\Sdk\Revision\Connector\Doctrine\RevisionDoctrineConnector',
            array('getCollection'),
            array($this->em, $this->logger)
        );

        $connector->expects($this->any())
            ->method('getCollection')
            ->will($this->returnValue($revisionsPublished));

        $this->assertSame($revisionsPublished, $connector->getRevisionsPublished());
    }
}
