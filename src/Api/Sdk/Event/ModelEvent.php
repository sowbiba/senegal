<?php

namespace Api\Sdk\Event;
use Api\Sdk\SdkInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Events are triggered by sdks.
 */
class ModelEvent extends Event
{
    /**
     * @var SdkInterface
     */
    private $sdk;

    /**
     * @var BaseModel|array
     */
    private $data;

    /**
     * Constructor
     *
     * @param object                      $entity
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(SdkInterface $sdk, $data)
    {
        $this->sdk  = $sdk;
        $this->data = $data;
    }

    /**
     * @return array|BaseModel
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return SdkInterface
     */
    public function getSdk()
    {
        return $this->sdk;
    }
}
