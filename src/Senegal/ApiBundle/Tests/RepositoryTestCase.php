<?php

namespace Senegal\ApiBundle\Tests;

abstract class RepositoryTestCase extends BaseFunctionalTestCase
{
    /**
     * @return \Senegal\ApiBundle\Repository\ContractSetRepository
     */
    public function getContractSetRepository()
    {
        return $this->getEntityManager()->getRepository(parent::$contractSetClassName);
    }

    /**
     * @return \Senegal\ApiBundle\Repository\ContractSetIdentityRepository
     */
    public function getContractSetIdentityRepository()
    {
        return $this->getEntityManager()->getRepository(parent::$contractSetIdentityClassName);
    }

    /**
     * @return \Senegal\ApiBundle\Repository\GroupRepository
     */
    public function getGroupRepository()
    {
        return $this->getEntityManager()->getRepository(parent::$groupClassName);
    }

    /**
     * @return \Senegal\ApiBundle\Repository\UserRepository
     */
    public function getUserRepository()
    {
        return $this->getEntityManager()->getRepository(parent::$userClassName);
    }

    /**
     * @return \Senegal\ApiBundle\Repository\VersionRepository
     */
    public function getVersionRepository()
    {
        return $this->getEntityManager()->getRepository(parent::$versionClassName);
    }

    /**
     * @return \Senegal\ApiBundle\Repository\ZoneRepository
     */
    public function getZoneRepository()
    {
        return $this->getEntityManager()->getRepository(parent::$zoneClassName);
    }

    /**
     * @return \Senegal\ApiBundle\Repository\ConcurrencyRepository
     */
    public function getConcurrencyRepository()
    {
        return $this->getEntityManager()->getRepository(parent::$concurrencyClassName);
    }

    /**
     * @return \Senegal\ApiBundle\Repository\ContractRepository
     */
    public function getContractRepository()
    {
        return $this->getEntityManager()->getRepository(parent::$contractClassName);
    }

    /**
     * @return \Senegal\ApiBundle\Repository\RapprochementSetRepository
     */
    public function getRapprochementSetRepository()
    {
        return $this->getEntityManager()->getRepository(parent::$rapprochementSetClassName);
    }
}
