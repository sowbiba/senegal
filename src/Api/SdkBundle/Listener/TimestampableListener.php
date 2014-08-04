<?php

namespace Api\SdkBundle\Listener;

use Api\Sdk\Event\ModelEvent;
use Api\Sdk\Model\TimestampableInterface;

/**
 * Class TimestampableListener
 */
class TimestampableListener
{
    /**
     * @param ModelEvent $args
     */
    public function preCreate(ModelEvent $args)
    {
        $object = $args->getData();

        if ($object instanceof TimestampableInterface) {
            $object->setCreatedAt(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
            $object->setUpdatedAt(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        }
    }

    /**
     * @param ModelEvent $args
     */
    public function preUpdate(ModelEvent $args)
    {
        $object = $args->getData();

        if ($object instanceof TimestampableInterface) {
            $object->setUpdatedAt(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        }
    }
}
