<?php

namespace Senegal\ApiBundle\Listener\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Senegal\ApiBundle\Entity\RapprochementSet;

class RapprochementSetListener
{
    /**
     * @param RapprochementSet   $rapprochementSet
     * @param LifecycleEventArgs $event
     */
    public function preRemove(RapprochementSet $rapprochementSet, LifecycleEventArgs $event)
    {
        $version = $rapprochementSet->getVersion();
        $version->setUpdatedAt(new \DateTime());
    }
}
