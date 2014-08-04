<?php

namespace Api\SdkBundle\Entity;

use Doctrine\ORM\PersistentCollection;

/**
 * Api\SdkBundle\Entity\BaseEntity
 *
 * @codeCoverageIgnore
 */
abstract class BaseEntity implements EntityInterface
{
    protected $id;
    protected $createdAt;
    protected $updatedAt;

    public function __construct()
    {
        $now = new \DateTime(null, new \DateTimeZone('Europe/Paris'));

        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Convert current object into an array
     *
     * @param  bool  $clearRelationsObject
     * @return array
     */
    public function toArray($clearRelationsObject = true)
    {
        $array = array();

        foreach ($this as $attribute => $data) {
            if ($clearRelationsObject) {
                if (!is_object($data) || $data instanceof \DateTime) {
                    $array[$attribute] = $data;
                }
            } else { // Transform object(s) to array(s)
                if ($data instanceof PersistentCollection) {
                    // Transform collection of objects to collection of array
                    $array[$attribute] = $this->hydrateArray($data);
                } elseif ($data instanceof BaseEntity) {
                    // Transform entity to array
                    $array[$attribute] = $data->toArray($clearRelationsObject);
                } elseif (is_array($data)) {
                    foreach ($data as $key => $object) {
                        $array[$attribute][$key] = $object->toArray($clearRelationsObject);
                    }
                } else {
                    $array[$attribute] = $data;
                }
            }
        }

        return $array;
    }

    /**
     * Create array for relation collections
     *
     * @param $collection
     * @return array
     */
    public function hydrateArray($collection)
    {
        $newCollection = array();

        foreach ($collection as $object) {
            if (is_object($object)) {
                $newCollection[] = $object->toArray();
            }
        }

        return $newCollection;
    }

    /**
     * @param $data
     */
    public function populate($data)
    {
        foreach ($data as $method => $value) {
            $setMethod = sprintf('set%s', ucfirst($method));

            if (method_exists($this, $setMethod) && null !== $value) {
                $this->$setMethod($value);
            }
        }
    }
}
