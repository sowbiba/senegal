<?php

namespace Api\Sdk\Model;

/**
 * User role
 */
class Role extends BaseModel
{
    /**
     * Identifiant
     *
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * Description
     *
     * @var string
     */
    protected $description;

    /**
     * Returns identifiant
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set an identifiant
     *
     * @return int
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set a description
     *
     * @return int
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * Set an identifiant
     *
     * @return int
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns the description of the role
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getDescription();
    }
}
