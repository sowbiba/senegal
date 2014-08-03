<?php

namespace Senegal\Api\SdkBundle\Entity;

/**
 * Senegal\Api\SdkBundle\Entity\Field
 *
 * @codeCoverageIgnore
 */
class Field extends BaseEntity
{
    protected $id;
    protected $name;
    protected $typeId;
    protected $unit;
    protected $chapterId;
    protected $displayOrder;
    protected $listId;
    protected $isSourceable;
    protected $businessDefinition;
    protected $integrationRule;

    /**
     * @param mixed $listId
     */
    public function setListId($listId)
    {
        $this->listId = $listId;
    }

    /**
     * @return mixed
     */
    public function getListId()
    {
        return $this->listId;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @return int
     */
    public function getChapterId()
    {
        return $this->chapterId;
    }

    /**
     * @return int
     */
    public function getDisplayOrder()
    {
        return $this->displayOrder;
    }

    /**
     * @return boolean
     */
    public function isSourceable()
    {
        return $this->isSourceable;
    }

    /**
     * @return string
     */
    public function getBusinessDefinition()
    {
        return $this->businessDefiniton;
    }

    /**
     * @return string
     */
    public function getIntegrationRule()
    {
        return $this->integrationRule;
    }

}
