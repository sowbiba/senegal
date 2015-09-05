<?php

namespace Senegal\ApiBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class FilterManagerEvent extends Event
{
    private $entity;

    private $data;

    /**
     * @param null  $entity
     * @param array $data
     */
    public function __construct($entity = null, array $data = [])
    {
        $this->entity = $entity;
        $this->data = $data;
    }

    /**
     * @param $entity
     *
     * @return FilterManagerEvent
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @return null|object
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param $data
     *
     * @return FilterManagerEvent
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
