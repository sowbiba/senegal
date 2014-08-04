<?php
namespace Api\Sdk\Tests\Document;

use Api\Sdk\Document\Query\DocumentQuery;
use Api\Sdk\Tests\SdkTestCase;

class DocumentSdkTest extends SdkTestCase
{

    /**
     * Just test that we get a Document.
     * Tests on getters are done in DocumentTest
     */
    public function testGetById()
    {
        $document = $this->getSdk("document")->getById(1);

        $this->assertInstanceOf('Api\Sdk\Model\Document', $document);
        $this->assertEquals(1, $document->getId());
    }

    /**
     * Test that we get Documents, all and only the documents asked for
     */
    public function testGetByIds()
    {
        $documentsIdsToGet = array(1, 2);

        // ensure we get only documents
        $documentsRetrieved = $this->getSdk("document")->getByIds($documentsIdsToGet);
        $this->assertContainsOnly('Api\Sdk\Model\Document', $documentsRetrieved);

        // then get unique documents ids
        $documentsIdsRetrieved = array();
        foreach( $documentsRetrieved as $document ) {
            $documentsIdsRetrieved[] = $document->getId();
        }
        $documentsIdsRetrieved = array_unique( $documentsIdsRetrieved );
        // and check we got the right number of documents
        $this->assertCount(count($documentsIdsToGet), $documentsIdsRetrieved);

        // finally, check that we got the documents that were asked for
        foreach( $documentsIdsRetrieved as $documentIdRetrieved ) {
            $this->assertContains($documentIdRetrieved, $documentsIdsToGet);
        }
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testGetByIdWithBadParameters()
    {
        $this->getSdk("document")->getById("un");
    }

    public function testGetType()
    {
        $document = $this->getSdk("document")->getById(1);

        $documentType = $document->getType();

        $this->assertInstanceOf('Api\Sdk\Model\DocumentType', $documentType);
        $this->assertEquals(1, $documentType->getId());
        $this->assertEquals('type A', $documentType->getName());
    }

    public function testGetDocuments()
    {
        $documents = $this->getSdk("document")->getCollection(new DocumentQuery());
        foreach ($documents as $document) {
            $this->assertInstanceOf('Api\Sdk\Model\Document', $document);
        }
    }

    public function testCountDocument()
    {
        $this->assertTrue(is_integer($this->getSdk("document")->count(new DocumentQuery())));
    }

    public function testGetRevisionsDocument()
    {
        $document  = $this->getSdk("document")->getById(1);
        $revisions = $this->getSdk("document")->getRevisions($document);

        $this->assertEquals(count($revisions), 1);

        array_walk($revisions, function ($revision) {
            $this->assertInstanceOf('Api\Sdk\Model\Revision', $revision);
        });
    }

    public function dataProviderFieldValue()
    {
        $data = array(
            [1, 1, 1, 'valeur du champ 1'],
            [1, 1, 2, 206],
            [1, 1, 3, 5],
            [1, 1, 6, 1380536040], //=> '2013-09-30 12:14:00'
            [1, 1, 0, null],
        );

        return $data;
    }

    public function testGetUserWithNullParameter()
    {
        $this->assertNull($this->getSdk("document")->getUser(null));
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testGetUserWithBadParameter()
    {
        $this->getSdk("document")->getUser("p_carole");
    }

    public function testDocumentAlreadyExistsWithNonExistentFilePathReturnFalse()
    {
        $this->assertFalse($this->getSdk("document")->alreadyExists('tata.ext'));
    }

    public function testDocumentAlreadyExistsWithExistingFilePathReturnTrue()
    {
        $this->assertTrue($this->getSdk("document")->alreadyExists('document1.ext'));
    }

}
