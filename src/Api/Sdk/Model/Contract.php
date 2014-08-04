<?php

/**
 * A contract is defined by its membership in a product line
 * that gives a tree structure of chapters containing fields
 *
 * @link https://github.com/Profideo/schoko-backoffice/wiki/Contrat
 *
 */

namespace Api\Sdk\Model;

use Api\Sdk\Query\QueryInterface;
use Api\Sdk\SdkInterface;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Class Contract
 * @ExclusionPolicy("all")
 */
class Contract extends BaseModel
{
    /**
     * @var int
     * @Expose
     */
    protected $id;
    /**
     * @var string
     * @Expose
     */
    protected $name;
    /**
     * @var string
     * @Expose
     */
    protected $fullName;
    /**
     * @var string
     * @Expose
     */
    protected $planName;
    /**
     * @var int
     * @Expose
     */
    protected $planNumber;
    /**
     * @var int
     * @Expose
     */
    protected $planTotalNumber;
    /**
     * @var int
     * @Expose
     */
    protected $planActive;
    /**
     * @var int
     * @Expose
     */
    protected $productLineId;
    /**
     * @var int
     * @Expose
     */
    protected $isMarketed;
    /**
     * @var int
     * @Expose
     */
    protected $isActive;
    protected $isParent;
    protected $hasParent;
    protected $siblings;
    protected $distributors;
    protected $insurers;
    protected $parentId;
    protected $isMirror;

    /**
     * @Expose
     */
    protected $releasedAt;

    protected $isFuture;

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

    const NOT_CHILD_INHERITANCE = "not_child";
    const CHILD_INHERITANCE = "child";

    /**
     * Constructor.
     *
     * @param SdkInterface $sdk
     * @param array $properties values of properties in keys (@example : ['id' => 1, 'name' => 'Contract #1'])
     */
    public function __construct(SdkInterface $sdk, array $properties = array())
    {
        $this->siblings = array();
        $this->distributors = array();
        $this->insurers = array();

        parent::__construct($sdk, $properties);
    }

    /**
     * Set Contract's id.
     *
     * @param int $id
     *
     * @return Contract current instance
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set Contract's name.
     *
     * @param string $name
     *
     * @return Contract current instance
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set FullName's planName.
     *
     * @param $fullName
     *
     * @return string
     */
    public function setFullName($fullName)
    {
        return $this->fullName = $fullName;
    }

    /**
     * Set Contract's planName.
     *
     * @param string $planName
     *
     * @return Contract current instance
     */
    public function setPlanName($planName)
    {
        $this->planName = $planName;

        return $this;
    }

    /**
     * Set Contract's planNumber.
     *
     * @param int $planNumber
     *
     * @return Contract current instance
     */
    public function setPlanNumber($planNumber)
    {
        $this->planNumber = $planNumber;

        return $this;
    }

    /**
     * Set Contract's planTotalNumber.
     *
     * @param int $planTotalNumber
     *
     * @return Contract current instance
     */
    public function setPlanTotalNumber($planTotalNumber)
    {
        $this->planTotalNumber = $planTotalNumber;

        return $this;
    }

    /**
     * @param $planActive
     */
    public function setPlanActive($planActive)
    {
        $this->planActive = $planActive;
    }

    /**
     * @return mixed
     */
    public function getPlanActive()
    {
        return $this->planActive;
    }

    /**
     * Set Contract's isMarketed.
     *
     * @param boolean $isMarketed
     *
     * @return Contract current instance
     */
    public function setIsMarketed($isMarketed)
    {
        $this->isMarketed = $isMarketed;

        return $this;
    }

    /**
     * Set Contract's isActive.
     *
     * @param boolean $isActive
     *
     * @return Contract current instance
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Set Contract's isParent.
     *
     * @param boolean $isParent
     *
     * @return Contract current instance
     */
    public function setIsParent($isParent)
    {
        $this->isParent = $isParent;

        return $this;
    }

    /**
     * Set Contract's hasParent.
     *
     * @param boolean $hasParent
     *
     * @return Contract current instance
     */
    public function setHasParent($hasParent)
    {
        $this->hasParent = $hasParent;

        return $this;
    }

    /**
     * Set contract's productLineId.
     *
     * @param array $productLine
     *
     * @return Contract current instance
     */
    public function setProductLine(array $productLine)
    {
        $this->setProductLineId($productLine['id']);

        return $this;
    }

    /**
     * Set contract's productLineId.
     *
     * @param int $productLineId
     *
     * @return Contract current instance
     */
    public function setProductLineId($productLineId)
    {
        $this->productLineId = $productLineId;

        return $this;
    }

    /**
     * Set Contract's siblings.
     *
     * @param array $siblings array of Contract
     *
     * @return Contract current instance
     *
     * @throws \InvalidArgumentException when $siblings is not an array of Contract
     */
    public function setSiblings($siblings)
    {
        if (empty($siblings)) {
            return;
        }

        foreach ($siblings as $sibling) {
            if (!($sibling instanceof Contract)) {
                throw new \InvalidArgumentException(get_class($sibling) . " is not instance of " . get_class($this));
            }
        }

        $this->siblings = $siblings;

        return $this;
    }

    /**
     * Set Contract's distributors.
     *
     * @param array $distributors array of data
     *
     * @return array $distributors array of Company
     *
     * @throws \InvalidArgumentException when $distributors is not an array of Company
     */
    public function setDistributors(array $distributors)
    {
        if (empty($distributors)) {
            return;
        }

        $finalDistributors = array();

        foreach ($distributors as $distributor) {
            $finalDistributors[] = new Company($this->sdk, $distributor);
        }

        $this->distributors = $finalDistributors;

        return $this;
    }

    /**
     * Set Contract's insurers.
     *
     * @param array $insurers array of data
     *
     * @return array $insurers array of Company
     *
     * @throws \InvalidArgumentException when $insurers is not an array of Company
     */
    public function setInsurers(array $insurers)
    {
        if (empty($insurers)) {
            return;
        }

        $finalInsurers = array();

        foreach ($insurers as $insurer) {
            $finalInsurers[] = new Company($this->sdk, $insurer);
        }

        $this->insurers = $finalInsurers;

        return $this;
    }

    /**
     * Sets the contract's parent id.
     *
     * @param int $id
     *
     * @return Contract current instance
     */
    public function setParentId($id)
    {
        $this->parentId = $id;

        return $this;
    }

    /**
     * Returns contract's id.
     *
     * @return int
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * Returns contract's name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns contract's fullName
     * If contract is future, returns fullname of its current contract
     *
     * @return string
     */
    public function getFullName()
    {
        if($this->isFuture()) {
            return $this->getCurrent()->getFullName();
        }

        return $this->fullName;
    }

    /**
     * @see \Api\Sdk\Contract\ContractSdk::getCurrent
     */
    public function getCurrent()
    {
        return $this->sdk->getCurrent($this);
    }

    /**
     * Returns contract's planName.
     *
     * @return string
     */
    public function getPlanName()
    {
        return $this->planName;
    }

    /**
     * Returns contract's planNumber.
     *
     * @return int
     */
    public function getPlanNumber()
    {
        return $this->planNumber;
    }

    /**
     * Returns contract's planTotalNumber.
     *
     * @return int
     */
    public function getPlanTotalNumber()
    {
        return $this->planTotalNumber;
    }

    /**
     * Returns the contract's parent id.
     *
     * @return int
     */
    public function getParentId()
    {
        return (integer)$this->parentId;
    }

    /**
     * Returns contract's productLine's id.
     *
     * @return int
     */
    public function getProductLineId()
    {
        return $this->productLineId;
    }

    /**
     * Returns contract's productLine.
     *
     * @return ProductLine
     */
    public function getProductLine()
    {
        return $this->sdk->getProductLine($this);
    }

    /**
     * Returns contract's distributors.
     *
     * @return array
     */
    public function getDistributors()
    {
        return $this->distributors;
    }

    /**
     * Returns contract's distributors.
     *
     * @return array
     */
    public function getInsurers()
    {
        return $this->insurers;
    }

    /**
     * Returns contract's isOnSale.
     *
     * @return boolean
     */
    public function isMarketed()
    {
        return $this->isMarketed;
    }

    /**
     * Returns contract's isActive.
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * Returns contract's isParent.
     *
     * @return boolean
     */
    public function isParent()
    {
        return $this->isParent;
    }

    /**
     * Returns contract's hasParent.
     *
     * @return boolean
     */
    public function hasParent()
    {
        return $this->hasParent;
    }

    /**
     * Returns inheritance status name
     *
     * @return string
     */
    public function getInheritanceStatusName()
    {
        return $this->isParent ? 'Père' : ($this->hasParent ? 'Fils' : null);
    }

    /**
     * Returns one revision of contract by number, or null when this revision does not exist.
     *
     * @param $number
     *
     * @return null|\Api\Sdk\Model\Revision
     */
    public function getRevision($number)
    {
        return $this->sdk->getRevisionByNumber($this, intval($number));
    }

    /**
     * Returns all revisions of contract.
     *
     * @return Array of \Api\Sdk\Model\Revision
     */
    public function getRevisions()
    {
        return $this->sdk->getRevisions($this);
    }

    /**
     * @return bool
     */
    public function hasRevisions()
    {
        return \count($this->getRevisions()) ? true : false;
    }

    /**
     * Check if contract can have new revision : it must not have in progress or submitted revision
     *
     * @return bool
     */
    public function canHaveNewRevision()
    {
        return null === $this->getRevisionInProgress() && null === $this->getSubmittedRevision() && null === $this->getPendingPublicationRevision();
    }

    /**
     * Get a revision contract with the given status
     *
     * @param $revisionStatus
     *
     * @return \Api\Sdk\Model\Revision|null
     */
    public function getRevisionWithStatus($revisionStatus)
    {
        return $this->sdk->getRevisionWithStatus($this, $revisionStatus);
    }

    /**
     * Get the published revision contract
     *
     * @return Revision
     */
    public function getPublishedRevision()
    {
        return $this->getRevisionWithStatus(Revision::STATUS_PUBLISHED);
    }

    /**
     * Get the contract revision in progress
     *
     * @return Revision
     */
    public function getRevisionInProgress()
    {
        return $this->getRevisionWithStatus(Revision::STATUS_IN_PROGRESS);
    }

    /**
     * Check if contract has submitted revision
     *
     * @return Revision
     */
    public function getSubmittedRevision()
    {
        return $this->getRevisionWithStatus(Revision::STATUS_SUBMITTED);
    }

    /**
     * Check if contract has pending publication revision
     *
     * @return Revision
     */
    public function getPendingPublicationRevision()
    {
        return $this->getRevisionWithStatus(Revision::STATUS_PENDING_PUBLICATION);
    }

    /**
     * Returns documents contract
     *
     * @return array of Api\Sdk\Model\Document
     */
    public function getDocuments(QueryInterface $query = null)
    {
        return $this->sdk->getDocuments($this, $query);
    }

    /**
     * Check if the contract has at least one document
     *
     * @return bool
     */
    public function hasDocument()
    {
        return count($this->getDocuments()) > 0;
    }

    /**
     * @return mixed
     */
    public function getIsMirror()
    {
        return $this->sdk->isMirror($this);
    }

    /**
     * @param \DateTime $releasedAt
     */
    public function setReleasedAt(\DateTime $releasedAt)
    {
        $this->releasedAt = $releasedAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getReleasedAt()
    {
        return $this->releasedAt;
    }

    /**
     * Released a contract
     *
     * @param Revision $revision
     */
    public function release()
    {
        $this->sdk->release($this);
    }

    /**
     * return contract fullname
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getFullName();
    }

    /**
     * Return all field sources
     * Contract field sources are the one of its published revision
     *
     * @return array
     */
    public function getFieldSources()
    {
        $publishedRevision = $this->getPublishedRevision();

        if (null !== $publishedRevision) {
            return $publishedRevision->getFieldSources();
        }

        return array();
    }

    /**
     * Returns the contracts which this one is parent of
     *
     * @return array Api\Sdk\Model\Contract[]
     */
    public function getChildren()
    {
        return $this->sdk->getChildren($this);
    }

    /**
     * Return true if the contract is child of an other contract, false otherwise
     *
     * @return bool
     */
    public function isChild()
    {
        $parentId = $this->getParentId();

        return !empty($parentId);
    }

    /**
     * Returns identifiants of inherited fields of the current contract
     *
     * @return array
     */
    public function getInheritedFieldsIds()
    {
        return $this->sdk->getInheritedFieldsIds($this);
    }

    /**
     * Return the contract's inherited chapters
     *
     * @return array
     */
    public function getInheritedChapters()
    {
        return $this->sdk->getInheritedChapters($this);
    }

    /**
     * Link chapters to a contract
     *
     * @param \Api\Sdk\Model\Contract $contract
     * @param array $chapters \Api\Sdk\Model\Chapter[]
     *
     * @return bool|array True when success, false when error or an array of
     *                    errors messages when integration rules errors
     */
    public function linkChapters(array $chapters)
    {
        return $this->sdk->linkChapters($this, $chapters);
    }

    /**
     * Return parent contract or null when it has not one
     *
     * @param \Api\Sdk\Model\Contract $contract
     *
     * @return \Api\Sdk\Model\Contract|null
     */
    public function getParent()
    {
        return $this->sdk->getParent($this);
    }

    /**
     * Check weither a contract inherit his eventual parent documents
     *
     * A contract inherits parent documents when it is explicitly defined in its
     * properties or when it is a child and its product line is revisionable
     *
     * @return bool
     */
    public function inheritsDocuments()
    {
        return $this->inheritsDocuments || $this->isChild() && $this->getProductLine()->isRevisionable();
    }

    /**
     * Make inherits document of its eventual parent contract
     *
     * @return \Api\Sdk\Model\Contract Current instance
     */
    public function setInheritsDocuments($inheritsDocuments)
    {
        $this->inheritsDocuments = $inheritsDocuments;

        return $this;
    }

    /**
     * Check weither a contract inherit his eventual parent funds
     *
     * @return bool
     */
    public function inheritsFunds()
    {
        return $this->inheritsFunds;
    }

    /**
     * Make inherits funds of its eventual parent contract
     *
     * @param bool $inheritsFunds
     * @return \Api\Sdk\Model\Contract Current instance
     */
    public function setInheritsFunds($inheritsFunds)
    {
        $this->inheritsFunds = $inheritsFunds;

        return $this;
    }

    /**
     * Returns opened revisions @see \Api\Sdk\Model\Revision::isOpened
     *
     * @param \Api\Sdk\Model\Contract $contract
     *
     * @return \Api\Sdk\Model\Revision|null
     */
    public function getOpenedRevision()
    {
        foreach($this->getRevisions() as $revision) {
            if($revision->isOpened()) {
                return $revision;
            }
        }

        return null;
    }

    /**
     * @see ContratSdk::setParent
     *
     * @param \Api\Sdk\Model\Contract $parent Parent contract to set
     */
    public function setParent(Contract $parent)
    {
        $this->sdk->setParent($this, $parent);
    }

    /**
     * @see ContractSdk::createRevision
     *
     * @return \Api\Sdk\Model\Revision
     */
    public function createRevision()
    {
        return $this->sdk->createRevision($this);
    }

    /**
     * Returns true if the contract is future, false otherwise
     *
     * @return bool
     */
    public function isFuture()
    {
        return $this->isFuture;
    }

    /**
     * Sets if the contract is future
     *
     * @param bool $isFuture
     *
     */
    public function setIsFuture($isFuture)
    {
        $this->isFuture = $isFuture;
    }
}
