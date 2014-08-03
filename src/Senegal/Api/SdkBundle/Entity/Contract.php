<?php

namespace Senegal\Api\SdkBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Senegal\Api\SdkBundle\Entity\Contract
 *
 * @codeCoverageIgnore
 */
class Contract extends BaseEntity
{
    protected $name;
    protected $planName;
    protected $planNumber;
    protected $planTotalNumber;
    protected $planActive;
    protected $productLine;
    protected $isMarketed;
    protected $isActive;
    protected $parentId;
    protected $isParent;
    protected $siblings;
    protected $distributors;
    protected $insurers;
    protected $documents;
    protected $fullName;
    protected $releasedAt;
    protected $revisions;
    protected $currentId;

    /**
     * Inherits documents of the eventual parent contract
     *
     * @var bool
     */
    protected $inheritsDocuments;

    /**
     * Inherits funds of the eventual parent contract
     *
     * @var bool
     */
    protected $inheritsFunds;

    // Added just for legacy \o/
    protected $isFuture;

    public function __construct()
    {
        parent::__construct();

        $this->documents = new ArrayCollection();
    }

    /**
     * Doctrine event
     *
     * Need to have array on relations for Model
     */
    public function postLoadHydrate()
    {
        $this->fullName = $this->buildDisplayName();

        return $this;
    }

    /**
     * @return mixed
     */
    public function getInsurers()
    {
        return $this->insurers;
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @return mixed
     */
    public function getIsMarketed()
    {
        return $this->isMarketed;
    }

    /**
     * @return mixed
     */
    public function getIsParent()
    {
        return $this->isParent;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getPlanName()
    {
        return $this->planName;
    }

    /**
     * @return mixed
     */
    public function getPlanNumber()
    {
        return $this->planNumber;
    }

    /**
     * @return mixed
     */
    public function getPlanTotalNumber()
    {
        return $this->planTotalNumber;
    }

    /**
     * @return mixed
     */
    public function getPlanActive()
    {
        return $this->planActive;
    }

    /**
     * @return mixed
     */
    public function getProductLine()
    {
        return $this->productLine;
    }

    /**
     * @return mixed
     */
    public function getSiblings()
    {
        return $this->siblings;
    }

    /**
     * @return mixed
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @return ArrayCollection
     */
    public function getDistributors()
    {
        return $this->distributors;
    }

    /**
     * @param ArrayCollection $documents
     */
    public function setDocuments($documents)
    {
        $this->documents = $documents;
    }

    /**
     * @return ArrayCollection
     */
    public function getDocuments()
    {
        return $this->documents;
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
     * @param ArrayCollection $revisions
     */
    public function setRevisions($revisions)
    {
        $this->revisions = $revisions;
    }

    /**
     * @return ArrayCollection
     */
    public function getRevisions()
    {
        return $this->revisions;
    }

    /**
     * @param Revision $document
     *
     * @return ArrayCollection
     */
    public function addRevision(Revision $revision)
    {
        $this->revisions[] = $revision;

        return $this->revisions;
    }

    /**
     * @return string
     */
    private function buildDisplayName()
    {
        if ($this->planActive) {
            if (strlen($this->planName)) {
                return sprintf('%s - %s - %s/%s', $this->name, $this->planName, $this->planNumber, $this->planTotalNumber);
            } else {
                return sprintf('%s - %s/%s', $this->name, $this->planNumber, $this->planTotalNumber);
            }
        }

        return $this->name;
    }

    /**
     * @param \DateTime $releasedAt
     */
    public function setReleasedAt(\DateTime $releasedAt)
    {
        $this->releasedAt = $releasedAt;
    }

    /**
     * @return mixed
     */
    public function getReleasedAt()
    {
        return $this->releasedAt;
    }

    /**
     * Check weither a contract inherits parent documents
     *
     * @return bool
     */
    public function inheritsDocuments()
    {
        return $this->inheritsDocuments;
    }

    /**
     * Check weither a contract inherits parent funds
     *
     * @return bool
     */
    public function inheritsFunds()
    {
        return $this->inheritsFunds;
    }

    /**
     * Set parent identifiant
     *
     * @param int $parentId
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * Set is parent
     *
     * @param bool $isParent
     */
    public function setIsParent($isParent)
    {
        $this->isParent = $isParent;
    }

    /**
     * Returns identifiant of current contract if current is future
     *
     * @return int|null
     */
    public function getCurrentId()
    {
        return $this->currentId;
    }
}
