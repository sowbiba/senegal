<?php

namespace Api\Sdk\Model;

class Company extends BaseModel
{
    private $id;
    private $name;

    /**
     * Return company's name
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set company's id
     *
     * @param  int                    $id
     * @return \Api\Sdk\Model\Company
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
     * Set company's name
     *
     * @param  string                 $name
     * @return \Api\Sdk\Model\Company
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
     * Returns company's id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns company's name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
