<?php

namespace Senegal\Api\SdkBundle\Listener;

use Pfd\Sdk\Event\ModelEvent;
use Pfd\Sdk\Model\TimestampableInterface;

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
