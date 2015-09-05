<?php

namespace Senegal\ApiBundle\Listener\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Senegal\ApiBundle\Entity\Group;

class GroupListener
{
    /**
     * @param Group              $group
     * @param LifecycleEventArgs $event
     */
    public function preRemove(Group $group, LifecycleEventArgs $event)
    {
        $userProfiles = $group->getUserProfiles();

        foreach ($userProfiles as $userProfile) {
            $userProfile->setGroup(null);
        }
    }
}
