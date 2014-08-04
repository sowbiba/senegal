<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nmoulin
 * Date: 13/09/13
 * Time: 10:20
 * To change this template use File | Settings | File Templates.
 */

namespace Api\Sdk\Tests\Document\Connector\Doctrine;

use Api\Sdk\Document\Connector\Doctrine\DocumentDoctrineConnector;
use Api\Sdk\Document\Connector\DocumentConnector;

use Api\SdkBundle\Entity\DocumentType;
use Api\SdkBundle\Entity\Document as DocumentEntity;
use Api\Sdk\Tests\SdkTestCase;

class DocumentDoctrineConnectorTest extends SdkTestCase
{
    private $em;
    private $repository;
    private $connector;
    private $sdk;

    public function setUp()
    {
        $this->initMocks();
    }

    public function testCreateAndUpdateDocument()
    {
        $data = array(
            'reference' => "reference createDocument",
            'description' => "description createDocument",
            'releasedAt' => null
        );

        $document = $this->connector->createDocument($data);

        $this->assertEquals($document['reference'], $data['reference']);
        $this->assertEquals($document['description'], $data['description']);
    }

    public function testUpdateDocument()
    {
        $data = array(
            'id' => 1,
            'reference' => "reference updateDocument",
            'description' => "description updateDocument",
            'releasedAt' => null,
        );

        $type = new DocumentType();
        $type->setId(1);
        $type->setName("type testGetDocument");

        $documentEntity = new DocumentEntity();
        $documentEntity->setId($data['id']);
        $documentEntity->setType($type);
        $documentEntity->setReference($data['reference']);
        $documentEntity->setDescription($data['description']);

        $this->repository->expects($this->once())
            ->method("find")
            ->will($this->returnValue($documentEntity));

        $documentUpdated = $this->connector->updateDocument($data);

        $this->assertEquals($documentUpdated['reference'], $data['reference']);
        $this->assertEquals($documentUpdated['description'], $data['description']);
    }

    public function testGetDocument()
    {
        $type = new DocumentType();
        $type->setId(1);
        $type->setName("type testGetDocument");

        $documentEntity = new DocumentEntity();
        $documentEntity->setId(1);
        $documentEntity->setType($type);
        $documentEntity->setReference("reference testGetDocument");
        $documentEntity->setDescription("description testGetDocument");

        $this->repository->expects($this->once())
            ->method("find")
            ->will($this->returnValue($documentEntity));

        $document = $this->connector->getById(1);

        $this->assertSame($document['id'], $documentEntity->getId());
        $this->assertSame($document['reference'], $documentEntity->getReference());
        $this->assertSame($document['description'], $documentEntity->getDescription());
        $this->assertInternalType('array', $document['type']);
        $this->assertArrayHasKey('id', $document['type']);
        $this->assertArrayHasKey('name', $document['type']);
        $this->assertSame($document['type']['id'], 1);
        $this->assertSame($document['type']['name'], "type testGetDocument");
    }

    public function testGetInvalideDocument()
    {

        $this->repository->expects($this->once())
            ->method("find")
            ->will($this->returnValue(array()));

        $document = $this->connector->getById(1);

        $this->assertNull($document);
    }

    public function testDeleteDocument()
    {
        $documentEntity = new DocumentEntity();
        $documentEntity->setId(1);

        $this->repository->expects($this->once())
            ->method("find")
            ->will($this->returnValue($documentEntity));

        $result = $this->connector->deleteDocument(1);

        $this->assertTrue($result);
    }

    public function testAlreadyExistsWithExistingFilePath()
    {
        $documentEntity = new DocumentEntity();
        $documentEntity->setId(1);
        $documentEntity->setFilePath('document.txt');

        $this->repository->expects($this->once())
            ->method("findOneBy")
            ->will($this->returnValue($documentEntity));

        $this->assertTrue($this->connector->alreadyExists('document1.txt'));
    }

    public function testAlreadyExistsWithNonExistentFilePath()
    {
        $this->repository->expects($this->once())
            ->method("findOneBy")
            ->will($this->returnValue(null));

        $this->assertFalse($this->connector->alreadyExists('document1.txt'));
    }

    private function initMocks()
    {
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->em->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($this->repository));

        $this->connector = new DocumentConnector(array(new DocumentDoctrineConnector($this->em)));
        $this->sdk = $this->getMockBuilder("Api\Sdk\SdkInterface")
            ->disableOriginalConstructor()
            ->getMock();
    }

}
