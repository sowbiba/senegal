<?php

/**
 * A product line corresponding to a type of product. A product line is a set of contract.
 *
 * @doc https://github.com/Profideo/schoko-backoffice/wiki/Gamme
 *
 */

namespace Api\Sdk\Model;

class ProductLine extends BaseModel
{
    protected $id;
    protected $name;
    protected $rootChapterId;
    protected $isRevisionable = false;
    protected $allowFutureContracts;
    protected $ruleSettingStatus;

    /**
     * Returns productLine's id
     *
     * @return int
     */
    public function getId()
    {
        return (int) $this->id;
    }

    /**
     * Set productLine's id
     *
     * @param  int                                   $id
     * @return \Api\ContractBundle\Model\ProductLine
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Returns productLine's name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set productLine's name
     *
     * @param  string                                $name
     * @return \Api\ContractBundle\Model\ProductLine
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set root chapter
     *
     * @param int
     * @return \Api\ContractBundle\Model\ProductLine
     *
     * Can not test the setter because getter is private...
     * @codeCoverageIgnore
     */
    public function setRootChapterId($chapterId)
    {
        $this->rootChapterId = $chapterId;

        return $this;
    }

    /**
     * Returns $this product line whole chapter tree
     */
    public function getChapterTree()
    {
        return $this->sdk->getChapterTree($this);
    }

    /**
     * Returns a list of first chapter object
     *
     * @return array
     */

    public function getFirstChapterLevel () {
      return $this->getChapterTree()->getChildren();
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Returns fields ids for the current product line
     *
     * @param  bool  $onlyEnabledFields => Only non-excluded fields if values true, all fields otherwise
     * @return array
     */
    public function getFieldIds($onlyEnabledFields = false)
    {
        return $this->sdk->getFieldIds($this->getId(), $onlyEnabledFields);
    }

    /**
     * Return target fields ids of current product line
     *
     * @return int[] ids of target fields
     */
    public function getTargetFieldIds()
    {
        return $this->sdk->getTargetFieldIds($this);
    }

    /**
     * Return source fields ids of current product line
     *
     * @return int[] ids of source fields
     */
    public function getSourceFieldIds()
    {
        return $this->sdk->getSourceFieldIds($this);
    }

    /**
     * @return bool
     */
    public function isRevisionable()
    {
        return $this->isRevisionable;
    }

    /**
     * @param bool $isRevisionable
     */
    public function setIsRevisionable($isRevisionable)
    {
        $this->isRevisionable = $isRevisionable;
    }

    /**
     * @param mixed $allowFutureContracts
     */
    public function setAllowFutureContracts($allowFutureContracts)
    {
        $this->allowFutureContracts = $allowFutureContracts;
    }

    /**
     * @return mixed
     */
    public function allowFutureContracts()
    {
        return $this->allowFutureContracts;
    }

    /**
     * @param bool $ruleSettingStatus
     */
    public function setRuleSettingStatus($ruleSettingStatus)
    {
        $this->ruleSettingStatus = $ruleSettingStatus;
    }

    /**
     * @return mixed
     */
    public function ruleSettingStatus()
    {
        return $this->ruleSettingStatus;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'id'               => $this->id,
            'name'             => $this->name,
            'chapitresroot_id' => $this->rootChapterId,
            'is_revisionable'  => $this->isRevisionable ? 1 : 0,
        );
    }
}
