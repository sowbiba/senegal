<?php

namespace Senegal\Api\SdkBundle\Security\User;

use Pfd\Sdk\Model\User;
use Pfd\Sdk\Bridge\LegacyBridge;
use Pfd\Sdk\SdkInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * Class SdkUserProvider
 */
class SdkUserProvider implements UserProviderInterface
{
    /**
     * @var \Pfd\Sdk\SdkInterface
     */
    protected $sdk;

    /**
     * @var \Pfd\Sdk\Bridge\LegacyBridge
     */
    protected $bridge;

    /**
     * @param SdkInterface $sdk
     */
    public function __construct(SdkInterface $sdk, LegacyBridge $bridge)
    {
        $this->sdk = $sdk;
        $this->bridge = $bridge;
    }

    /**
     * @param string $username
     *
     * @return User
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function loadUserByUsername($username)
    {
        /** @var User $user */
        $user = $this->sdk->getByUsername($username);
        if (null === $user) {
            $user = $this->sdk->getByEmail($username);
        }

        if (null === $user || !$user->isActive()) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return $user;
    }

    /**
     * @param UserInterface $user
     *
     * @return User
     * @throws \Symfony\Component\Security\Core\Exception\UnsupportedUserException
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $refreshedUser = $this->loadUserByUsername($user->getUsername());

        // Ensure that the user is logged in the legacy
        $this->bridge->permissiveTransaction(function () use ($refreshedUser) {
            $user = \sfGuardUserPeer::retrieveByPK($refreshedUser->getId());
            \sfContext::getInstance()->getUser()->signIn($user);
        });

        return $refreshedUser;
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class === 'Pfd\Sdk\Model\User';
    }
}
