<?php

namespace Senegal\Api\SdkBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Pfd\Sdk\Model\Revision as ModelRevision;

/**
 * Senegal\Api\SdkBundle\Entity\Revision
 */
class Revision extends BaseEntity
{
    protected $contract;
    protected $number;
    protected $status;
    protected $publishedAt;
    protected $createdBy;
    protected $updatedBy;
    protected $publishedBy;
    protected $documents;
    protected $isCloned = false;
    protected $isLocked = false;

    public function __construct()
    {
        parent::__construct();

        $this->status    = ModelRevision::STATUS_IN_PROGRESS;
        $this->number    = 1;
        $this->documents = new ArrayCollection();
    }

    /**
     * Convert current object into an array
     *
     * @param  bool  $clearRelationsObject
     * @return array
     */
    public function toArray($clearRelationsObject = true)
    {
        return array_merge(parent::toArray($clearRelationsObject), array(
            'contractId' => $this->getContract() ? $this->getContract()->getId() : null
        ));
    }

    /**
     * @param Contract $contract
     */
    public function setContract(Contract $contract)
    {
        $this->contract = $contract;
    }

    /**
     * @return Contract
     */
    public function getContract()
    {
        return $this->contract;
    }

    /**
     * @param $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
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
     * @param $publishedBy
     */
    public function setPublishedBy($publishedBy)
    {
        $this->publishedBy = $publishedBy;
    }

    /**
     * @return mixed
     */
    public function getPublishedBy()
    {
        return $this->publishedBy;
    }

    /**
     * @param $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param $updatedBy
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;
    }

    /**
     * @return mixed
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * @param $documents
     */
    public function setDocuments($documents)
    {
        $this->documents = $documents;
    }

    /**
     * @param Document $document
     *
     * @return ArrayCollection
     */
    public function addDocument(Document $document)
    {
        $this->documents[] = $document;

        return $this->documents;
    }

    /**
     * @return ArrayCollection
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     *  Clear all documents of the revision
     */
    public function clearDocuments()
    {
        $this->documents->clear();
    }

    /**
     * Checks if it is read-only
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
     * Set cloned status
     *
     * @see RevisionSdk::setAsClone to know what is a cloned revision
     *
     * @param bool $isCloned
     */
    public function setIsCloned($isCloned)
    {
        $this->isCloned = $isCloned;
    }

    /**
     * Checks if revision is locked
     *
     * @return bool
     */
    public function isLocked()
    {
        return $this->isLocked;
    }

    /**
     * Set as locked or unlock
     *
     * @param bool $isLocked
     */
    public function setIsLocked($isLocked)
    {
        $this->isLocked = $isLocked;
    }
}
