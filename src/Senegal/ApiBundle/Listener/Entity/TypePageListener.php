<?php

namespace Senegal\ApiBundle\Listener\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Senegal\ApiBundle\Entity\TypePage;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TypePageListener
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
     * @param TypePage               $typePage
     * @param LifecycleEventArgs $event
     */
    public function prePersist(TypePage $typePage, LifecycleEventArgs $event)
    {
        //$typePage->setUpdatedAt(new \DateTime());
    }
}
