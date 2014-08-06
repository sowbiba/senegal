<?php
/**
 * Author: Florent Coquel
 * Date: 31/10/13
 */

namespace Api\Sdk\User\Connector;

use Api\Sdk\Connector\AbstractConnector;
use Api\Sdk\Model\User;

class UserConnector extends AbstractConnector
{
    /**
     * @param string $username
     *
     * @return array|null
     */
    public function getByUsername($username)
    {

        $user = $this->getConnectorToUse("getByUsername")->getByUsername($username);

        return (null !== $user) ? $this->convertPermissionsToRoles($user) : null;
    }

    /**
     * @param string $email
     *
     * @return array|null
     */
    public function getByEmail($email)
    {

        $user = $this->getConnectorToUse("getByEmail")->getByEmail($email);

        return (null !== $user) ? $this->convertPermissionsToRoles($user) : null;
    }

    /**
     * @see sfGuardUser::getBackofficeRole
     * @param User $user
     *
     * @return mixed
     */
    public function getBackOfficeRole(User $user)
    {
        return $this->getConnectorToUse("getBackOfficeRole")->getBackOfficeRole($user);
    }

    /**
     * Convert the legacy permissions to SF2 roles
     *
     * @param array $user
     *
     * @return array
     */
    private function convertPermissionsToRoles(Array $user)
    {
        foreach ($user['roles'] as $key => $permission) {
            $user['roles'][$key] = 'ROLE_'.strtoupper($permission['name']);
        }

        return $user;
    }

    /**
     * @see sfGuardUser
     *
     * @param User $user
     *
     * @return mixed
     */
    public function hasLtaCredentials(User $user)
    {
        return $this->getConnectorToUse("hasLtaCredentials")->hasLtaCredentials($user);
    }

    /**
     * @see sfGuardUser
     *
     * @param User $user
     *
     * @return mixed
     */
    public function hasDistribCredentials(User $user)
    {
        return $this->getConnectorToUse("hasDistribCredentials")->hasDistribCredentials($user);
    }

    /**
     * @see sfGuardUser
     *
     * @param User $user
     *
     * @return mixed
     */
    public function hasLtaInvoice(User $user)
    {
        return $this->getConnectorToUse("hasLtaInvoice")->hasLtaInvoice($user);
    }

    /**
     * @see sfGuardUser
     *
     * @param User $user
     *
     * @return mixed
     */
    public function hasDistribInvoice(User $user)
    {
        return $this->getConnectorToUse("hasDistribInvoice")->hasDistribInvoice($user);
    }

    /**
     * Set roles to a user in specific scopes
     * Delete scopes linked with the user and add the roles provided
     *
     * @param Api\Sdk\Model\User   $user   User concerned
     * @param Api\Sdk\Model\Role[] $roles  Roles to set
     * @param array                $scopes example : espri, espri_back, backoffice, lta_user
     *
     * @return void
     */
    public function setRoles(User $user, array $roles, array $scopes)
    {
        return $this->getConnectorToUse("setRoles")->setRoles($user, $roles, $scopes );
    }

    /**
     * Calls the user propel connector one
     *
     * @param int $id User identifiant
     */
    public function getById($id)
    {
        return $this->getMediator()->getColleague('userPropel')->getById($id);
    }

}
