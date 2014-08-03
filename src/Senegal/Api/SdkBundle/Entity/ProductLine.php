<?php

namespace Senegal\Api\SdkBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Senegal\Api\SdkBundle\Entity\ProductLine
 *
 * @codeCoverageIgnore
 */
class ProductLine extends BaseEntity
{
    protected $name;
    protected $isObsolete;
    protected $isRevisionable;
    protected $contracts;
    protected $chapterTree;
    protected $rootChapterId;
    protected $ruleSettingStatus;

    public function __construct()
    {
        $this->contracts = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getIsObsolete()
    {
        return $this->isObsolete;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return ArrayCollection
     */
    public function getContracts()
    {
        return $this->contracts;
    }

    /**
     * @return ArrayCollection
     */
    public function getChapterTree()
    {
        return $this->chapterTree;
    }

    /**
     * @return ArrayCollection
     */
    public function getRootChapterId()
    {
        return $this->rootChapterId;
    }

    /**
     * @return ArrayCollection
     */
    public function setRootChapterId($chapterId)
    {
        $this->rootChapterId = $chapterId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsRevisionable()
    {
        return $this->isRevisionable;
    }

    /**
     * @return mixed
     */
    public function getRuleSettingStatus()
    {
        return $this->ruleSettingStatus;
    }

}
