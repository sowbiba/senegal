<?php
namespace Api\Sdk\User;

use Api\Sdk\AbstractSdk;
use Api\Sdk\Model\User;
use Api\Sdk\SdkInterface;

/**
 * This class lists all the high-level methods for User objects.
 *
 * This class can only use POPO classes (\Api\Sdk\Model)
 * To use this class you have to initialize a connector (\Api\Sdk\Connector) and pass it to the constructor
 * These connectors work with POPO objects, to save an object you have to pass it
 * Only connectors can use entities (\Api\SdkBundle\Entity)
 *
 * Class UserSdk
 * @package Api\Sdk\User
 * @author  Florent Coquel
 * @since   17/09/13
 */
class UserSdk extends AbstractSdk implements SdkInterface
{
    /**
     * Returns user matching the given id
     *
     * @param int $userId
     *
     * @return null|User
     * @throws \BadMethodCallException
     */
    public function getById($userId)
    {
        if (!is_int($userId)) {
            throw new \BadMethodCallException(__METHOD__ . "(): Wrong parameter, userId must be an integer and valid !!!");
        }

        $userData = $this->connector->getById($userId);

        return empty($userData) ? null : new User($this, $userData);
        return empty($userData) ? null : $userData;
    }
    
    /**
     * Returns all users
     *
     *
     * @return Array
     */
    public function getAllUsers()
    {
        return $this->connector->getAllUsers();
    }

    /**
     * Returns user matching the given username
     *
     * @param string $username
     *
     * @return null|User
     */
    public function getByUsername($username)
    {
        $userData = $this->connector->getByUsername($username);

        return empty($userData) ? null : new User($this, $userData);
    }

    /**
     * Returns user matching the given username
     *
     * @param string $email
     *
     * @return null|User
     */
    public function getByEmail($email)
    {
        $userData = $this->connector->getByEmail($email);

        return empty($userData) ? null : new User($this, $userData);
    }

    /**
     * @param string $classname
     *
     * @return bool
     */
    public function supports($classname)
    {
        return $classname === 'Api\Sdk\Model\User';
    }

    /**
     * @see sfGuardUser::getBackofficeRole
     *
     * @param User $user
     *
     * @return mixed
     */
    public function getBackOfficeRole(User $user)
    {
        return $this->connector->getBackOfficeRole($user);
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
        return $this->connector->hasLtaCredentials($user);
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
        return $this->connector->hasDistribCredentials($user);
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
        return $this->connector->hasLtaInvoice($user);
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
        return $this->connector->hasDistribInvoice($user);
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
        $this->connector->setRoles($user, $roles, $scopes);
    }
    
    public function updateValues(User $user)
    {
        if (0 == (int)$user->getId()) {
            throw new \BadMethodCallException(__METHOD__ . "(): Wrong parameter, user id must be an integer!!!");
        }
        
        if(!$this->connector->updateValues($user)) {
            return false;
        }
        
        return true;
    }
}
