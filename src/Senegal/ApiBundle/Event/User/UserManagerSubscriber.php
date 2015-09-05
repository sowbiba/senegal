<?php

namespace Senegal\ApiBundle\Event\User;

use Senegal\ApiBundle\Entity\Group;
use Senegal\ApiBundle\Entity\User;
use Senegal\ApiBundle\Event\ApiEvents;
use Senegal\ApiBundle\Event\FilterManagerEvent;
use Senegal\ApiBundle\Manager\GroupManager;
use Senegal\ApiBundle\Manager\RoleManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserManagerSubscriber implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var RoleManager
     */
    private $roleManager;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->roleManager = $this->container->get('senegal_role_manager');
    }

    public static function getSubscribedEvents()
    {
        return [
            ApiEvents::PRE_MANAGER_USER_INSERT    => ['onPreManagerUserInsertUpdate', 0],
            ApiEvents::PRE_MANAGER_USER_UPDATE    => ['onPreManagerUserInsertUpdate', 0],
        ];
    }

    public function onPreManagerUserInsertUpdate(FilterManagerEvent $event)
    {
        $data = $event->getData();

        // We remove the password to avoid an empty password when the user is updated,
        // when it is empty or null
        if (isset($data['password']) && (null === $data['password'] || '' === $data['password'])) {
            unset($data['password']);
        }

        if (isset($data['role']) && null !== $data['role']) {
            $data['role'] = $this->roleManager->find($data['role']);
        }

        $event->setData($data);
    }
}
