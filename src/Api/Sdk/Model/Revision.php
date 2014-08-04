<?php
/**
 * Author: Florent Coquel
 * Date: 20/06/13
 */

namespace Api\Sdk\Model;

use Api\Sdk\Query\QueryInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Class Revision
 * @package Api\Sdk\Model
 * @ExclusionPolicy("all")
 */
class Revision extends BaseModel implements BlameableInterface, TimestampableInterface
{
    const STATUS_IN_PROGRESS = 0;
    const STATUS_PUBLISHED = 1;
    const STATUS_ARCHIVED = 2;
    const STATUS_SUBMITTED = 3;
    const STATUS_PENDING_PUBLICATION = 4;

    private static $statusLabel = array(
        self::STATUS_IN_PROGRESS => 'en cours',
        self::STATUS_PUBLISHED => 'publié',
        self::STATUS_ARCHIVED => 'archivé',
        self::STATUS_SUBMITTED => 'à valider',
        self::STATUS_PENDING_PUBLICATION => 'en attente de publication'
    );
    /**
     * @var
     */
    protected $id;
    /**
     * @var int
     * @Expose
     */
    protected $number;
    /**
     * @var int
     * @Expose
     */
    protected $status;
    /**
     * @var
     */
    protected $contractId;
    /**
     * @var
     */
    protected $createdAt;
    /**
     * @var
     */
    protected $updatedAt;
    /**
     * @var
     * @Expose
     */
    protected $publishedAt;
    protected $createdBy;
    protected $updatedBy;
    protected $publishedBy;
    protected $isCloned;
    protected $isLocked;

    /**
     * Set values in properties of current instance.
     *
     * @param array $properties
     *
     * @todo Should we lazy load the relation ?
     */
    public function createFromArray(array $properties)
    {
        if (isset($properties['createdBy'])) {
            $this->setCreatedBy($this->sdk->getUser($properties['createdBy']));
            unset($properties['createdBy']);
        }
        if (isset($properties['CreatedBy'])) {
            $this->setCreatedBy($this->sdk->getUser($properties['CreatedBy']));
            unset($properties['CreatedBy']);
        }

        if (isset($properties['updatedBy'])) {
            $this->setUpdatedBy($this->sdk->getUser($properties['updatedBy']));
            unset($properties['updatedBy']);
        }
        if (isset($properties['UpdatedBy'])) {
            $this->setUpdatedBy($this->sdk->getUser($properties['UpdatedBy']));
            unset($properties['UpdatedBy']);
        }

        parent::createFromArray($properties);
    }

    /**
     * @param $contractId
     */
    public function setContractId($contractId)
    {
        $this->contractId = $contractId;
    }

    /**
     * @return mixed
     */
    public function getContractId()
    {
        return (int)$this->contractId;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param UserInterface $createdBy
     */
    public function setCreatedBy(UserInterface $createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return UserInterface
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return !is_null($this->id) ? (int)$this->id : null;
    }

    /**
     * @param int $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return int
     */
    public function getNumber()
    {
        return (int)$this->number;
    }

    /**
     * @return int
     */
    public function getPreviousNumber()
    {
        return $this->number - 1;
    }

    /**
     * @return int
     */
    public function getNextNumber()
    {
        return $this->number + 1;
    }

    /**
     * @param $publishedAt
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;
    }

    /**
     * @return mixed
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @param int $publishedBy
     */
    public function setPublishedBy($publishedBy)
    {
        $this->publishedBy = $publishedBy;
    }

    /**
     * @return int
     */
    public function getPublishedBy()
    {
        return $this->publishedBy;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param UserInterface $updatedBy
     */
    public function setUpdatedBy(UserInterface $updatedBy)
    {
        $this->updatedBy = $updatedBy;
    }

    /**
     * @return UserInterface
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * @return Contract
     */
    public function getContract()
    {
        return $this->sdk->getContract($this);
    }

    /**
     * @return User
     */
    public function getPublisher()
    {
        return $this->sdk->getUser($this->getPublishedBy());
    }

    /**
     * @return bool
     */
    public function canBeEdited()
    {
        return (self::STATUS_ARCHIVED !== $this->getStatus() && !$this->isLocked());
    }

    /**
     * Check if a revision can be deleted depending on its status.
     *
     * @return bool
     */
    public function canBeDeleted()
    {
        return !$this->isCloned() && $this->isInProgress() && !$this->isLocked();
    }

    /**
     * Check if a revision can be published.
     *
     * @return bool
     */
    public function canBePublished()
    {
        if(!$this->isPendingPublication()) {
            return false;
        }

        foreach($this->getContract()->getChildren() as $childContract) {
            $childRevision = $childContract->getRevision($this->getNumber());
            if(!$childRevision || !$childRevision->isPendingPublication()) {
                return false;
            }            
        }
        return true;
    }

    /**
     * Check if a revision can be put pending publication.
     *
     * @return bool
     */
    public function canBePutPendingPublication()
    {
        return  !$this->isCloned() && $this->isSubmitted() && !$this->isLocked();
    }

    /**
     * Check if a revision can be unpublished.
     * You can not unpublish a revision while there are a current
     */
    public function canBeUnpublished()
    {
        return !$this->isCloned() && !$this->isLocked() &&
               null === $this->getContract()->getRevisionWithStatus(self::STATUS_IN_PROGRESS) &&
               $this->isPublished();
    }

    /**
     * Check if a revision can be submitted
     *
     * @return bool
     */
    public function canBeSubmitted()
    {
        return !$this->isCloned() && $this->isInProgress() && !$this->isLocked();
    }

    /**
     * Check if a revision can be rejected
     *
     * @return bool
     */
    public function canBeRejected()
    {
        return !$this->isCloned() && $this->isSubmitted() && !$this->isLocked();
    }

    /**
     * Check if a revision can be rejected while pending publication
     *
     * @return bool
     */
    public function canBeRejectedPendingPublication()
    {
        return !$this->isCloned() && $this->isPendingPublication() && !$this->isLocked();
    }

    /**
     * Submit this revision.
     *
     * @return bool
     */
    public function submit()
    {
        if ($this->sdk->submit($this)) {
            $this->setStatus(self::STATUS_SUBMITTED);

            return true;
        }

        return false;
    }

    /**
     * Reject this revision.
     *
     * @return bool
     */
    public function reject()
    {
        if ($this->sdk->reject($this)) {
            $this->setStatus(self::STATUS_IN_PROGRESS);

            return true;
        }

        return false;
    }

    /**
     * Reject this revision.
     *
     * @return bool
     */
    public function rejectPendingPublication()
    {
        if ($this->sdk->rejectPendingPublication($this)) {
            $this->setStatus(self::STATUS_SUBMITTED);

            return true;
        }

        return false;
    }

    /**
     * Put pending publication this revision
     *
     * @return bool
     */
    public function putPendingPublication()
    {
        if ($this->sdk->putPendingPublication($this)) {
            $this->setStatus(self::STATUS_PENDING_PUBLICATION);

            return true;
        }

        return false;
    }

    /**
     * Publish this revision.
     *
     * @return bool
     */
    public function publish()
    {
        if ($this->sdk->publish($this)) {
            $this->setStatus(self::STATUS_PUBLISHED);

            return true;
        }

        return false;
    }

    /**
     * Unpublish this revision.
     *
     * @return bool
     */
    public function unpublish()
    {
        if ($this->sdk->unpublish($this)) {
            $this->setStatus(self::STATUS_IN_PROGRESS);

            return true;
        }

        return false;
    }

    /**
     * Check if Revision is published.
     *
     * @return bool
     */
    public function isPublished()
    {
        return self::STATUS_PUBLISHED === $this->getStatus();
    }

    /**
     * Check if Revision is pending publication.
     *
     * @return bool
     */
    public function isPendingPublication()
    {
        return self::STATUS_PENDING_PUBLICATION === $this->getStatus();
    }

    /**
     * Check if Revision is in progress.
     *
     * @return bool
     */
    public function isInProgress()
    {
        return self::STATUS_IN_PROGRESS === $this->getStatus();
    }

    /**
     * Check if Revision is archived
     *
     * @return bool
     */
    public function isArchived()
    {
        return self::STATUS_ARCHIVED === $this->getStatus();
    }

    /**
     * Check if the revision is submitted
     *
     * @return bool
     */
    public function isSubmitted()
    {
        return self::STATUS_SUBMITTED === $this->getStatus();
    }

    /**
     * Return text of label for a status.
     *
     * @return mixed
     */
    public function getStatusLabel()
    {
        return self::$statusLabel[$this->getStatus()];
    }

    /**
     * Check if the revision is linked to a document
     *
     * @param Document $document
     *
     * @return bool
     */
    public function hasDocument(Document $document)
    {
        return $this->sdk->hasDocument($this, $document);
    }

    /**
     * Link the revision to documents
     *
     * @param array $documents \Api\Sdk\Model\Document[]
     */
    public function linkDocuments(array $documents)
    {
        $this->sdk->linkDocuments($this, $documents);
    }

    /**
     * Return documents revision
     *
     * @return Document[]
     */
    public function getDocuments(QueryInterface $query = null)
    {
        return $this->sdk->getDocuments($this, $query);
    }

    /**
     * Get all field's values
     *
     * @return array
     */
    public function getValues()
    {
        return $this->sdk->getValues($this);
    }

    /**
     * @return bool
     */
    public function delete()
    {
        return $this->sdk->delete($this);
    }

    /**
     * Return field by id
     *
     * @param int $fieldId
     *
     * @return null|\Api\Sdk\Model\Field
     */
    public function getField($fieldId)
    {
        return $this->sdk->getField($fieldId);
    }

    /**
     * @param integer $fieldId
     *
     * @return mixed
     */
    public function getFieldValue($fieldId)
    {
        return $this->sdk->getFieldValue($this, $fieldId);
    }

    /**
     * Compare given (posted) data with expected data for the product line of the given revision
     *
     * @param array $data
     *
     * @return bool true if $data is correct, else false
     */
    public function validateFields(array $data)
    {
        return $this->sdk->validateFields($this, $data);
    }

    /**
     * Return field source for the given Field
     *
     * @param Field $field
     *
     * @return mixed
     */
    public function getFieldSource(Field $field)
    {
        return $this->sdk->getFieldSource($this, $field);
    }

    /**
     * Return all field source related to this revision
     *
     * @return array
     */
    public function getFieldSources()
    {
        return $this->sdk->getFieldSources($this);
    }

    /**
     * @param Document $document
     *
     * @return boolean
     */
    public function hasSourcesForDocument(Document $document)
    {
        return $this->sdk->hasSourcesForDocument($this, $document);
    }

    /**
     * Checks if it is opened
     * An open revision revision is one which is not published and not archived
     *
     * @return bool
     */
    public function isOpened()
    {
        return !$this->isPublished() && !$this->isArchived();
    }

    /**
     * @see RevisionSdk::setAsClone
     *
     * @throws Api\Sdk\SdkException When it is not opened or in pending publication or has not previous
     */
    public function setAsClone()
    {
        $this->sdk->setAsClone($this);
    }

    /**
     * @see RevisionSdk::reset
     *
     * @throws \Api\Sdk\SdkException When it is not opened
     */
    public function reset()
    {
        $this->sdk->reset($this);
    }

    /**
     * Setter for cloned status
     *
     * @see RevisionSdk::setAsClone to know what is a cloned revision
     *
     * @param bool $isCloned
     *
     * @return \Api\Sdk\Model\Revision
     */
    public function setIsCloned($isCloned)
    {
        $this->isCloned = $isCloned;

        return $this;
    }

    /**
     * Checks if it is cloned
     *
     * @see RevisionSdk::setAsClone to know what is a cloned revision
     *
     * @return bool
     */
    public function isCloned()
    {
        return $this->isCloned;
    }

    /**
     * Setter for locked status
     *
     * @param bool $isLocked
     * @return \Api\Sdk\Model\Revision
     */
    public function setIsLocked($isLocked)
    {
        $this->isLocked = $isLocked;

        return $this;
    }

    /**
     * Check if the revision is locked
     *
     * @return bool
     */
    public function isLocked()
    {
        return $this->isLocked;
    }

    /**
     * @see RevisionSdk::updateValues
     *
     * @codeCoverageIgnore
     */
    public function updateValues($values)
    {
        return $this->sdk->updateValues($this, $values);
    }

    /**
     * Copy all field source document of other revision
     *
     * @param \Api\Sdk\Model\Revision $revision Revision source
     *
     * @codeCoverageIgnore
     */
    public function copyFieldsSources(Revision $revision)
    {
        foreach($revision->getFieldSources() as $fieldSource) {
            $fieldSource->setId(null)->setRevisionId($this->getId())->save();
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = parent::toArray();
        $data['createdBy'] = $this->getCreatedBy() ? $this->getCreatedBy()->getId() : null;
        $data['updatedBy'] = $this->getUpdatedBy() ? $this->getUpdatedBy()->getId() : null;
        $data['updatedAt'] = $this->getUpdatedAt();
        $data['createdAt'] = $this->getCreatedAt();

        return $data;
    }
}
