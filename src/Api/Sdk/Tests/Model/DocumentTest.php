<?php

namespace Api\Sdk\Tests\Model;

use Api\Sdk\Model\Document;
use Api\Sdk\Tests\SdkTestCase;

class DocumentTest extends SdkTestCase
{
    /**
     * @return array
     */
    public function getData()
    {
        $documentTypeData = [
            'id' => 1,
            'name' => 'type A'
        ];

        return array(
            'id'           => 1,
            'type'         => $documentTypeData,
            'filePath'     => "import/documents/doc.pdf",
            'reference'    => "reference doc.pdf",
            'description'  => "description doc.pdf",
            'createdBy'    => 1,
            'size'         => 206,
            'releasedAt'   => null,
            'createdAt'    => "2013-07-08 00:00:00",
            'updatedAt'    => "2013-07-08 00:00:00",
        );
    }

    /**
     * @return Revision
     */
    public function createFormArray()
    {
        return new Document($this->getSdk("document"), $this->getData());
    }

    /**
     * At creation, must return good values
     */
    public function testCreateReturnGoodValues()
    {
        $document     = $this->createFormArray();
        $documentData = $this->getData();

        $this->assertEquals($document->getId(), $documentData['id']);
        $this->assertEquals($document->getType()->getId(), $documentData['type']['id']);
        $this->assertEquals($document->getFilePath(), $documentData['filePath']);
        $this->assertEquals($document->getFileName(), 'doc.pdf');
        $this->assertEquals($document->getReference(), $documentData['reference']);
        $this->assertEquals($document->getDescription(), $documentData['description']);
        $this->assertEquals($document->getSize(), $documentData['size']);
        $this->assertEquals($document->getReleasedAt(), $documentData['releasedAt']);
        $this->assertEquals($document->getCreatedAt(), $documentData['createdAt']);
        $this->assertEquals($document->getUpdatedAt(), $documentData['updatedAt']);

        $this->assertInstanceOf('Api\Sdk\Model\User', $document->getCreatedBy(), 'Creator is a User object');
    }

    public function testGetRevisions()
    {
        $document  = $this->getSdk("document")->getById(1);
        $revisions = $document->getRevisions();

        $this->assertEquals(count($revisions), 1);
        $this->assertInstanceOf('Api\Sdk\Model\Revision', $revisions[0]);
    }
}
