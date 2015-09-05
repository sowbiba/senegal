<?php

namespace Senegal\ApiBundle\Listener\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Senegal\ApiBundle\Entity\Forfait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ForfaitListener
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
     * @param Forfait               $forfait
     * @param LifecycleEventArgs $event
     */
    public function prePersist(Forfait $forfait, LifecycleEventArgs $event)
    {
        //$forfait->setUpdatedAt(new \DateTime());
    }
}
