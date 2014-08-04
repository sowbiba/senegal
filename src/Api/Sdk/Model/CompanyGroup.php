<?php

namespace Api\Sdk\Model;

class CompanyGroup extends BaseModel
{
    private $id;
    private $name;

    /**
     * Return company group's name
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set company group's id
     *
     * @param  int                         $id
     * @return \Api\Sdk\Model\CompanyGroup
     */
    public function setId($id)
    {
        if (!is_int($id)) {
            throw new \BadMethodCallException(__METHOD__ . ' expects an int as parameter, ' . gettype($id) . ' given');
        }

        $this->id = $id;

        return $this;
    }

    /**
     * Set company group's name
     *
     * @param  string                      $name
     * @return \Api\Sdk\Model\CompanyGroup
     */
    public function setName($name)
    {
        if (!is_string($name)) {
            throw new \BadMethodCallException(__METHOD__ . ' expects an int as parameter, ' . gettype($name) . ' given');
        }

        $this->name = $name;

        return $this;
    }

    /**
     * Returns company group's id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns company group's name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
