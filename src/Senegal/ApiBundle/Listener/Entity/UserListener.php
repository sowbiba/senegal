<?php

namespace Senegal\ApiBundle\Listener\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Senegal\ApiBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserListener
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param User               $user
     * @param LifecycleEventArgs $event
     */
    public function prePersist(User $user, LifecycleEventArgs $event)
    {
        $user->setUpdatedAt(new \DateTime());
    }
}
