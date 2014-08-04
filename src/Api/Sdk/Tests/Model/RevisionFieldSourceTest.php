<?php

namespace Api\Sdk\Tests\Model;

use Api\Sdk\Tests\SdkTestCase;
use \Api\Sdk\Model\RevisionFieldSource;

class RevisionFieldSourceTest extends SdkTestCase
{
    public function testCreateFromArray()
    {
        $data = [
            'id'         => 1,
            'revisionId' => 1,
            'fieldId'    => 1,
            'documentId' => 1,
            'page'       => 15

        ];
        // Create a fake contract for testing
        $revisionFieldSource = new RevisionFieldSource($this->getSdk("revision"), $data);
        // Assert
        $this->assertEquals($revisionFieldSource->getId(), $data['id']);
        $this->assertEquals($revisionFieldSource->getRevisionId(), $data['revisionId']);
        $this->assertEquals($revisionFieldSource->getFieldId(), $data['fieldId']);
        $this->assertEquals($revisionFieldSource->getDocumentId(), $data['documentId']);
        $this->assertEquals($revisionFieldSource->getPage(), $data['page']);
    }

    public function testCreateFromArrayWithUnderscores()
    {
        $data = [
            'id'         => 1,
            'revision_id' => 1,
            'field_id'    => 1,
            'document_id' => 1,
            'page'       => 15
        ];

        // Create a fake contract for testing
        $revisionFieldSource = new RevisionFieldSource($this->getSdk("revision"), $data);
        // Assert
        $this->assertEquals($revisionFieldSource->getId(), $data['id']);
        $this->assertEquals($revisionFieldSource->getRevisionId(), $data['revision_id']);
        $this->assertEquals($revisionFieldSource->getFieldId(), $data['field_id']);
        $this->assertEquals($revisionFieldSource->getDocumentId(), $data['document_id']);
        $this->assertEquals($revisionFieldSource->getPage(), $data['page']);
    }
}
