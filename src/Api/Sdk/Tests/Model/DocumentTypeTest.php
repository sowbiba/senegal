<?php

namespace Api\Sdk\Tests\Model;

use Api\Sdk\Model\DocumentType;
use Api\Sdk\Tests\SdkTestCase;

class DocumentTypeTest extends SdkTestCase
{
    /**
     * @return array
     */
    public function getData()
    {
        return [
            'id' => 1,
            'name' => 'type A'
        ];
    }

    /**
     * @return Revision
     */
    public function createFormArray()
    {
        return new DocumentType($this->getSdk("document"), $this->getData());
    }

    /**
     * At creation, must return good values
     */
    public function testCreateReturnGoodValues()
    {
        $document     = $this->createFormArray();
        $documentData = $this->getData();

        $this->assertEquals($document->getId(), $documentData['id']);
        $this->assertEquals($document->getName(), $documentData['name']);
    }
}
