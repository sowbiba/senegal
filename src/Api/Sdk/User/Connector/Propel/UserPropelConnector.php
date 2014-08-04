<?php
/**
 * Author: Florent Coquel
 * Date: 04/11/13
 */

namespace Api\Sdk\User\Connector\Propel;

use Api\Sdk\Connector\AbstractPropelConnector;
use Api\Sdk\Model\User;

class UserPropelConnector extends AbstractPropelConnector
{
    /**
     * @param int $userId
     *
     * @return array|\Api\Sdk\Connector\Api\Sdk\Model
     */
    public function getById($userId)
    {
        $user = $this->getBridge()->permissiveTransaction(function () use ($userId) {
            return \sfGuardUserPeer::retrieveByPK($userId);
        });

        return (null === $user) ? null : $this->convertUser($user);
    }

    /**
     * @param string $username
     *
     * @return array|null
     */
    public function getByUsername($username)
    {
        $user = $this->getBridge()->permissiveTransaction(function () use ($username) {
            return \sfGuardUserPeer::retrieveByUsername($username);
        });

        return (null === $user) ? null : $this->convertUser($user);
    }

    /**
     * @param string $email
     *
     * @return array|null
     */
    public function getByEmail($email)
    {
        $user = $this->getBridge()->permissiveTransaction(function () use ($email) {
            return \sfGuardUserPeer::retrieveByEmail($email);
        });

        return (null === $user) ? null : $this->convertUser($user);
    }

    /**
     * @see sfGuardUser::getBackofficeRole
     * @param User $user
     *
     * @return \Api\Sdk\Bridge\type
     */
    public function getBackOfficeRole(User $user)
    {
        return $this->getBridge()->permissiveTransaction(function () use ($user) {

            $sfGuardUser = \sfGuardUserPeer::retrieveByPk($user->getId());

            return $sfGuardUser->getBackOfficeRole();
        });
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
        return $this->getBridge()->permissiveTransaction(function () use ($user) {

            $sfGuardUser = \sfGuardUserPeer::retrieveByPk($user->getId());

            return $sfGuardUser->hasLtaCredentials();
        });
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
        return $this->getBridge()->permissiveTransaction(function () use ($user) {

            $sfGuardUser = \sfGuardUserPeer::retrieveByPk($user->getId());

            return $sfGuardUser->hasDistribCredentials();
        });
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
        return $this->getBridge()->permissiveTransaction(function () use ($user) {

            $sfGuardUser = \sfGuardUserPeer::retrieveByPk($user->getId());

            return $sfGuardUser->hasLtaInvoice();
        });
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
        return $this->getBridge()->permissiveTransaction(function () use ($user) {

            $sfGuardUser = \sfGuardUserPeer::retrieveByPk($user->getId());

            return $sfGuardUser->hasDistribInvoice();
        });
    }

    /**
     * Convert legacy users in an array
     *
     * @param \sfGuardUser $user
     *
     * @return array
     */
    public function convertUser(\sfGuardUser $user)
    {
        return [
            'id'            => $user->getId(),
            'username'      => $user->getUsername(),
            'firstname'     => $user->getFirstname(),
            'lastname'      => $user->getName(),
            'company'       => $user->getSociete(),
            'email'         => $user->getEmail(),
            'roles'         => array_values($user->getAllPermissions()),
            'type'          => $user->getType(),
            'salt'          => $user->getSalt(),
            'password'      => $user->getPassword(),
            'active'        => $user->getIsActive(),
        ];
    }
}
