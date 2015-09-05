<?php

namespace Senegal\ApiBundle\Tests;

abstract class ManagerTestCase extends BaseFunctionalTestCase
{
    /**
     * @return \Senegal\ApiBundle\Manager\ContractSetManager
     */
    public function getContractSetManager()
    {
        return $this->getContainer()->get('senegal_contract_set_manager');
    }

    /**
     * @return \Senegal\ApiBundle\Manager\ContractSetIdentityManager
     */
    public function getContractSetIdentityManager()
    {
        return $this->getContainer()->get('senegal_contract_set_identity_manager');
    }

    /**
     * @return \Senegal\ApiBundle\Manager\ContractSetZoneManager
     */
    public function getContractSetZoneManager()
    {
        return $this->getContainer()->get('senegal_contract_set_zone_manager');
    }

    /**
     * @return \Senegal\ApiBundle\Manager\GroupManager
     */
    public function getGroupManager()
    {
        return $this->getContainer()->get('senegal_group_manager');
    }

    /**
     * @return \Senegal\ApiBundle\Manager\ProductLineManager
     */
    public function getProductLineManager()
    {
        return $this->getContainer()->get('senegal_product_line_manager');
    }

    /**
     * @return \Senegal\ApiBundle\Manager\RoleManager
     */
    public function getRoleManager()
    {
        return $this->getContainer()->get('senegal_role_manager');
    }

    /**
     * @return \Senegal\ApiBundle\Manager\UserManager
     */
    public function getUserManager()
    {
        return $this->getContainer()->get('senegal_user_manager');
    }

    /**
     * @return \Senegal\ApiBundle\Manager\VersionManager
     */
    public function getVersionManager()
    {
        return $this->getContainer()->get('senegal_version_manager');
    }

    /**
     * @return \Senegal\ApiBundle\Manager\VersionHistoryManager
     */
    public function getVersionHistoryManager()
    {
        return $this->getContainer()->get('senegal_version_history_manager');
    }

    /**
     * @return \Senegal\ApiBundle\Manager\ZoneManager
     */
    public function getZoneManager()
    {
        return $this->getContainer()->get('senegal_zone_manager');
    }

    /**
     * @return \Senegal\ApiBundle\Manager\ConcurrencyManager
     */
    public function getConcurrencyManager()
    {
        return $this->getContainer()->get('senegal_concurrency_manager');
    }

    /**
     * @return \Senegal\ApiBundle\Manager\ContractManager
     */
    public function getContractManager()
    {
        return $this->getContainer()->get('senegal_contract_manager');
    }

    /**
     * @return \Senegal\ApiBundle\Manager\RapprochementSetManager
     */
    public function getRapprochementSetManager()
    {
        return $this->getContainer()->get('senegal_rapprochement_set_manager');
    }

    /**
     * @return \Senegal\ApiBundle\Manager\FormulaManager
     */
    public function getFormulaManager()
    {
        return $this->getContainer()->get('senegal_formulas_manager');
    }
}
