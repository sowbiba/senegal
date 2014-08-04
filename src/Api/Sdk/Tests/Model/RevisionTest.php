<?php

namespace Api\Sdk\Tests\Model;

use Api\Sdk\Model\Revision;
use Api\Sdk\Tests\SdkTestCase;

class RevisionTest extends SdkTestCase
{

    private $fields;

    public function setUp()
    {
        $this->fields['text']        = $this->getSdk("field")->getById(1);
        $this->fields['numeric']     = $this->getSdk("field")->getById(2);
        $this->fields['list']        = $this->getSdk("field")->getById(3);
        $this->fields['computed']    = $this->getSdk("field")->getById(4);
        $this->fields['multiselect'] = $this->getSdk("field")->getById(5);
        $this->fields['date']        = $this->getSdk("field")->getById(6);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return array(
            'id'          => 1,
            'number'      => 1,
            'status'      => Revision::STATUS_IN_PROGRESS,
            'contractId'  => 1,
            'createdAt'   => "2013-07-08 00:00:00",
            'createdBy'   => 1,
            'updatedAt'   => "2013-07-08 00:00:00",
            'updatedBy'   => 1,
            'publishedAt' => "2013-07-08 00:00:00",
            'publishedBy' => 1,
            'isCloned'  => false,
        );
    }

    /**
     * At creation, must return good values
     */
    public function testCreateReturnGoodValues()
    {
        $revision     = new Revision($this->getSdk("revision"), $this->getData());
        $revisionData = $this->getData();

        $this->assertEquals($revision->getId(), $revisionData['id']);
        $this->assertEquals($revision->getNumber(), $revisionData['number']);
        $this->assertEquals($revision->getStatus(), $revisionData['status']);
        $this->assertEquals($revision->getContractId(), $revisionData['contractId']);
        $this->assertEquals($revision->getCreatedAt(), $revisionData['createdAt']);
        $this->assertEquals($revision->getUpdatedAt(), $revisionData['updatedAt']);
        $this->assertEquals($revision->getPublishedAt(), $revisionData['publishedAt']);
        $this->assertEquals($revision->getPublishedBy(), $revisionData['publishedBy']);
        $this->assertEquals($revision->canBeDeleted(), true);
        $this->assertEquals($revision->getStatusLabel(), 'en cours');
        $this->assertEquals($revision->IsCloned(), false);

        $this->assertInstanceOf('Api\Sdk\Model\User', $revision->getCreatedBy());
        $this->assertInstanceOf('Api\Sdk\Model\User', $revision->getUpdatedBy());
    }

    public function testCanBeDeleted()
    {
        // not in progess revision
        $revision = $this->getSdk("revision")->getById(3);
        $this->assertFalse($revision->canBeDeleted());

        // in progress revision
        $revision = $this->getSdk("revision")->getById(2);
        $this->assertTrue($revision->canBeDeleted());
    }

    /**
     * Getter for creator, updater, publisher must return a User object
     */
    public function testUserObjectsAreCorrectlyHandles()
    {
        $revision  = new Revision($this->getSdk("revision"), $this->getData());
        $publisher = $revision->getPublisher();

        $this->assertNull(null, $publisher, 'Publisher is not a User object');
    }

    public function testCanBeEdited()
    {
        $archivedRevision = $this->getSdk("revision")->getById(3);
        $this->assertFalse($archivedRevision->canBeEdited());

        // revision is locked -> KO
        $openedRevision = $this->getSdk("revision")->getById(2);
        $openedRevision->setIsLocked(true);
        $this->assertFalse($openedRevision->canBeEdited());

        $publishedRevision = $this->getSdk("revision")->getById(1);
        $this->assertFalse($publishedRevision->canBeSubmitted());
    }

    public function testCanBePublished()
    {
        $submittedRevision = $this->getSdk("revision")->getById(4);
        $this->assertTrue($submittedRevision->canBeRejected());

        $pendingPublicationRevision = $this->getSdk("revision")->getById(6);
        $this->assertTrue($pendingPublicationRevision->canBeRejectedPendingPublication());
    }

    public function testCanBeSubmitted()
    {
        // not revision in progress -> KO
        $revision = $this->getSdk("revision")->getById(1);
        $this->assertFalse($revision->canBeSubmitted());

        // revision in progress -> OK
        $revision = $this->getSdk("revision")->getById(2);
        $this->assertTrue($revision->canBeSubmitted());

        // revision in progress but is cloned -> KO
        $revision->setIsCloned(true);
        $this->assertFalse($revision->canBeSubmitted());
    }

    public function testCanBeRejected()
    {
        // not submitted revision -> KO
        $revision = $this->getSdk("revision")->getById(1);
        $this->assertFalse($revision->canBeRejected());

        // submitted revision -> OK
        $revision = $this->getSdk("revision")->getById(4);
        $this->assertTrue($revision->canBeRejected());

        // submitted in progress but is cloned -> KO
        $revision->setIsCloned(true);
        $this->assertFalse($revision->canBeRejected());
    }

    public function testCanBeUnpublished()
    {
        // not published revision -> KO
        $revision = $this->getSdk("revision")->getById(2);
        $this->assertFalse($revision->canBeUnpublished());

        // published revision but a revision in progress exist in contract -> KO
        $revision = $this->getSdk("revision")->getById(2);
        $this->assertFalse($revision->canBeUnpublished());

        // published revision without revision in progress exist in contract -> OK
        $revision = $this->getSdk("revision")->getById(7);
        $this->assertTrue($revision->canBeUnpublished());

        // published revision revision but is cloned -> KO
        $revision->setIsCloned(true);
        $this->assertFalse($revision->canBeUnpublished());
    }

    /**
     * Test if return only one document and if it is an instance of Document
     */
    public function testGetDocuments()
    {
        $revision  = $this->getSdk("revision")->getById(1);
        $documents = $revision->getDocuments();

        $this->assertEquals(count($documents), 1);
        $this->assertInstanceOf('Api\Sdk\Model\Document', $documents[0]);
    }

    public function testGetField()
    {
        $revision  = $this->getSdk("revision")->getById(1);

        $field = $revision->getField(1);
        $this->assertInstanceOf('Api\Sdk\Model\Field', $field);
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testGetFieldFailed()
    {
        $revision  = $this->getSdk("revision")->getById(1);

        $field = $revision->getField("wrong parameter");
        $this->assertInstanceOf('Api\Sdk\Model\Field', $field);
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
            array(1, 1), // revision #1 has one source field
            array(2, 0), // revision #2 has not source field
        );
    }

    /**
     * @dataProvider getFieldSourcesProvider
     */
    public function testGetFieldSources($revisionId, $fieldSourcesCount)
    {
        $contract = $this->getSdk("revision")->getById($revisionId);
        $fieldSources = $contract->getFieldSources();

        $this->assertThat($fieldSources, new \PHPUnit_Framework_Constraint_IsType('array'));
        $this->assertCount($fieldSourcesCount, $fieldSources);

    }

    public function testCanBePutPendingPublication()
    {
        // not submitted revision -> KO
        $revisionInProgress = $this->getSdk("revision")->getById(2);
        $this->assertFalse($revisionInProgress->canBePutPendingPublication());

        // submitted revision -> OK
        $revisionSubmitted = $this->getSdk("revision")->getById(4);
        $this->assertTrue($revisionSubmitted->canBePutPendingPublication());

        // submitted revision but is cloned -> KO
        $revisionSubmitted->setIsCloned(true);
        $this->assertFalse($revisionSubmitted->canBePutPendingPublication());
    }

    public function testCanBeRejectedPendingPublication()
    {
        // not in pending publication -> KO
        $revision = $this->getSdk("revision")->getById(5);
        $this->assertFalse($revision->canBeRejectedPendingPublication());

        // in pending publication -> OK
        $revision = $this->getSdk("revision")->getById(6);
        $this->assertTrue($revision->canBeRejectedPendingPublication());

        // in pending publication but is cloned -> KO
        $revision->setIsCloned(true);
        $this->assertFalse($revision->canBeRejectedPendingPublication());
    }

    public function testIsOpened()
    {
        $archivedRevision = $this->getSdk("revision")->getById(3);
        $this->assertFalse($archivedRevision->isOpened());

        $publishedRevision = $this->getSdk("revision")->getById(1);
        $this->assertFalse($publishedRevision->isOpened());

        $submittedRevision = $this->getSdk("revision")->getById(4);
        $this->assertTrue($submittedRevision->isOpened());
    }

    public function testIsLocked()
    {
        $openedRevision = $this->getSdk("revision")->getById(10);
        $openedRevision->setIsLocked(true);
        $this->assertTrue($openedRevision->isLocked());

        $publishedRevision = $this->getSdk("revision")->getById(1);
        $this->assertFalse($publishedRevision->isOpened());

        $submittedRevision = $this->getSdk("revision")->getById(4);
        $this->assertTrue($submittedRevision->isOpened());
    }

    public function testSetAsCloneWithNotOpenedOne()
    {
        $revision = $this->getSdk("revision")->getById(1);
        $this->setExpectedException('Api\Sdk\SdkException', 'Cannot clone not opened revision');
        $revision->setAsClone();
    }

    public function testSetAsCloneWithOneInPendingPublication()
    {
        $revision = $this->getSdk("revision")->getById(6);
        $this->setExpectedException('Api\Sdk\SdkException', 'Cannot clone a revision in pending publication');
        $revision->setAsClone();
    }

    public function testSetAsCloneWithNotPublishedOne()
    {
        $revision = $this->getSdk("revision")->getById(10);
        $this->setExpectedException('Api\Sdk\SdkException', 'Contract of revision has not published one');
        $revision->setAsClone();
    }

    public function testResetWithNotOpenedOne()
    {
        $revision = $this->getSdk("revision")->getById(1);
        $this->setExpectedException('Api\Sdk\SdkException', 'Cannot reset a not opened revision');
        $revision->reset();
    }
}
