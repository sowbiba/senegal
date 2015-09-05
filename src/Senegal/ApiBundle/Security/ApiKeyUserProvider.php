<?php

namespace Senegal\ApiBundle\Security;

use Senegal\ApiBundle\Manager\UserManager;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class ApiKeyUserProvider implements UserProviderInterface
{
    private $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function getUsernameForApiKey($apiKey)
    {
        $user = $this->userManager->findOneBy(['token' => $apiKey]);

        return (null === $user) ? null : $user->getUsername();
    }

    public function loadUserByUsername($username)
    {
        return $this->userManager->loadUserByUsername($username);
    }

    public function refreshUser(UserInterface $user)
    {
        throw new UnsupportedUserException();
    }

    public function supportsClass($class)
    {
        return 'Senegal\ApiBundle\Entity\User' === $class;
    }
}
