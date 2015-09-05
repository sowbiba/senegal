<?php

namespace Senegal\BackBundle\Tests;

abstract class ManagerTestCase extends BaseFunctionalTestCase
{
    /**
     * @return \Senegal\BackBundle\Manager\ContractSetManager
     */
    public function getContractSetManager()
    {
        return $this->getContainer()->get('senegal_contract_set_manager');
    }

    /**
     * @return \Senegal\BackBundle\Manager\ContractSetIdentityManager
     */
    public function getContractSetIdentityManager()
    {
        return $this->getContainer()->get('senegal_contract_set_identity_manager');
    }

    /**
     * @return \Senegal\BackBundle\Manager\ContractSetZoneManager
     */
    public function getContractSetZoneManager()
    {
        return $this->getContainer()->get('senegal_contract_set_zone_manager');
    }

    /**
     * @return \Senegal\BackBundle\Manager\GroupManager
     */
    public function getGroupManager()
    {
        return $this->getContainer()->get('senegal_group_manager');
    }

    /**
     * @return \Senegal\BackBundle\Manager\ProductLineManager
     */
    public function getProductLineManager()
    {
        return $this->getContainer()->get('senegal_product_line_manager');
    }

    /**
     * @return \Senegal\BackBundle\Manager\RoleManager
     */
    public function getRoleManager()
    {
        return $this->getContainer()->get('senegal_role_manager');
    }

    /**
     * @return \Senegal\BackBundle\Manager\UserManager
     */
    public function getUserManager()
    {
        return $this->getContainer()->get('senegal_user_manager');
    }

    /**
     * @return \Senegal\BackBundle\Manager\VersionManager
     */
    public function getVersionManager()
    {
        return $this->getContainer()->get('senegal_version_manager');
    }

    /**
     * @return \Senegal\BackBundle\Manager\VersionHistoryManager
     */
    public function getVersionHistoryManager()
    {
        return $this->getContainer()->get('senegal_version_history_manager');
    }

    /**
     * @return \Senegal\BackBundle\Manager\ZoneManager
     */
    public function getZoneManager()
    {
        return $this->getContainer()->get('senegal_zone_manager');
    }

    /**
     * @return \Senegal\BackBundle\Manager\ConcurrencyManager
     */
    public function getConcurrencyManager()
    {
        return $this->getContainer()->get('senegal_concurrency_manager');
    }

    /**
     * @return \Senegal\BackBundle\Manager\ContractManager
     */
    public function getContractManager()
    {
        return $this->getContainer()->get('senegal_contract_manager');
    }

    /**
     * @return \Senegal\BackBundle\Manager\RapprochementSetManager
     */
    public function getRapprochementSetManager()
    {
        return $this->getContainer()->get('senegal_rapprochement_set_manager');
    }

    /**
     * @return \Senegal\BackBundle\Manager\FormulaManager
     */
    public function getFormulaManager()
    {
        return $this->getContainer()->get('senegal_formulas_manager');
    }
}
