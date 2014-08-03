<?php

namespace Senegal\Api\SdkBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Senegal\Api\SdkBundle\Entity\Chapter
 *
 * @codeCoverageIgnore
 */
class Chapter extends BaseEntity
{
    protected $id;
    protected $name;
    protected $level;
    protected $fields;
    protected $parentId;
    protected $productLineId;
    protected $children;
    protected $isTable;

    /**
     * @param mixed $isTable
     */
    public function setIsTable($isTable)
    {
        $this->isTable = $isTable;
    }

    /**
     * @return mixed
     */
    public function getIsTable()
    {
        return $this->isTable;
    }

    public function __construct()
    {
        $this->fields   = array();
        $this->children = array();
    }

    /**
     * Returns this chapter's id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns this chapter's name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns this chapter's level
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    public function addChild(Chapter $chapter)
    {
        $this->children[$chapter->id] = $chapter;
    }

    /**
     * Returns all $this chapter's children
     *
     * @return array()
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Returns this chapter's fields
     *
     * @return ArrayCollection
     */
    public function getFields()
    {
        return $this->fields;
    }

    public function addField($field)
    {
        $this->fields[] = ($field);
    }

    /**
     * Returns $this chapter's product line
     *
     * @return \ProductLine
     */
    public function getProductLineId()
    {
        return $this->productLineId;
    }

    /**
     * Returns $this chapter's parent
     *
     * @return \Chapter
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Tells us wether $this chapter has a parent or is a poor orphan
     *
     * @return boolean
     */
    public function hasParent()
    {
        return null !== $this->parentId;
    }

    /**
     * Tells us wether $this chapter has fields
     *
     * @return boolean
     */
    public function hasFields()
    {
        return count($this->fields) > 0;
    }
}
