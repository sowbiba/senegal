<?php

namespace Senegal\ApiBundle\Event;

use Doctrine\ORM\EntityManager;
use Senegal\ApiBundle\Manager\CompanyGroupManager;
use Senegal\ApiBundle\Manager\CompanyManager;
use Senegal\ApiBundle\Manager\ContractManager;
use Senegal\ApiBundle\Manager\ContractSetIdentityManager;
use Senegal\ApiBundle\Manager\ContractSetManager;
use Senegal\ApiBundle\Manager\ContractStatusManager;
use Senegal\ApiBundle\Manager\GroupManager;
use Senegal\ApiBundle\Manager\ProductLineManager;
use Senegal\ApiBundle\Manager\RapprochementSetManager;
use Senegal\ApiBundle\Manager\RoleManager;
use Senegal\ApiBundle\Manager\UserManager;
use Senegal\ApiBundle\Manager\VersionManager;
use Senegal\ApiBundle\Manager\ZoneManager;
use Senegal\ApiBundle\Workflow\Versioning\VersioningSubscriber;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class ApiSubscriber
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->container->get('doctrine.orm.entity_manager');
    }

    /**
     * @return RoleManager
     */
    protected function getRoleManager()
    {
        return $this->container->get('senegal_role_manager');
    }

    /**
     * @return UserManager
     */
    protected function getUserManager()
    {
        return $this->container->get('senegal_user_manager');
    }

    /**
     * @return ForfaitManager
     */
    protected function getForfaitManager()
    {
        return $this->container->get('senegal_forfait_manager');
    }
}
