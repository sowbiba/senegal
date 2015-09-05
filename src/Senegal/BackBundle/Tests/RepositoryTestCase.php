<?php

namespace Senegal\BackBundle\Tests;

abstract class RepositoryTestCase extends BaseFunctionalTestCase
{
    /**
     * @return \Senegal\BackBundle\Repository\ContractSetRepository
     */
    public function getContractSetRepository()
    {
        return $this->getEntityManager()->getRepository(parent::$contractSetClassName);
    }

    /**
     * @return \Senegal\BackBundle\Repository\ContractSetIdentityRepository
     */
    public function getContractSetIdentityRepository()
    {
        return $this->getEntityManager()->getRepository(parent::$contractSetIdentityClassName);
    }

    /**
     * @return \Senegal\BackBundle\Repository\GroupRepository
     */
    public function getGroupRepository()
    {
        return $this->getEntityManager()->getRepository(parent::$groupClassName);
    }

    /**
     * @return \Senegal\BackBundle\Repository\UserRepository
     */
    public function getUserRepository()
    {
        return $this->getEntityManager()->getRepository(parent::$userClassName);
    }

    /**
     * @return \Senegal\BackBundle\Repository\VersionRepository
     */
    public function getVersionRepository()
    {
        return $this->getEntityManager()->getRepository(parent::$versionClassName);
    }

    /**
     * @return \Senegal\BackBundle\Repository\ZoneRepository
     */
    public function getZoneRepository()
    {
        return $this->getEntityManager()->getRepository(parent::$zoneClassName);
    }

    /**
     * @return \Senegal\BackBundle\Repository\ConcurrencyRepository
     */
    public function getConcurrencyRepository()
    {
        return $this->getEntityManager()->getRepository(parent::$concurrencyClassName);
    }

    /**
     * @return \Senegal\BackBundle\Repository\ContractRepository
     */
    public function getContractRepository()
    {
        return $this->getEntityManager()->getRepository(parent::$contractClassName);
    }

    /**
     * @return \Senegal\BackBundle\Repository\RapprochementSetRepository
     */
    public function getRapprochementSetRepository()
    {
        return $this->getEntityManager()->getRepository(parent::$rapprochementSetClassName);
    }
}
