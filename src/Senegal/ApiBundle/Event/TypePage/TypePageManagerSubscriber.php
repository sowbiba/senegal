<?php

namespace Senegal\ApiBundle\Event\TypePage;

use Senegal\ApiBundle\Entity\TypePage;
use Senegal\ApiBundle\Event\ApiEvents;
use Senegal\ApiBundle\Event\FilterManagerEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TypePageManagerSubscriber implements EventSubscriberInterface
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

    public static function getSubscribedEvents()
    {
        return [
            ApiEvents::PRE_MANAGER_TYPEPAGE_INSERT    => ['onPreManagerTypePageInsertUpdate', 0],
            ApiEvents::PRE_MANAGER_TYPEPAGE_UPDATE    => ['onPreManagerTypePageInsertUpdate', 0],
        ];
    }

    public function onPreManagerTypePageInsertUpdate(FilterManagerEvent $event)
    {
        $data = $event->getData();

        $event->setData($data);
    }
}
