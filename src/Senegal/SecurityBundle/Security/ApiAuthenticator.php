<?php

namespace Senegal\SecurityBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\SimpleFormAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class ApiAuthenticator.
 *
 * @codeCoverageIgnore
 */
class ApiAuthenticator implements SimpleFormAuthenticatorInterface
{
    /**
     * @param Request $request
     * @param $username
     * @param $password
     * @param $providerKey
     *
     * @return UsernamePasswordToken
     */
    public function createToken(Request $request, $username, $password, $providerKey)
    {
        return new UsernamePasswordToken($username, $password, $providerKey);
    }

    /**
     * @param TokenInterface        $token
     * @param UserProviderInterface $userProvider
     * @param $providerKey
     *
     * @return UsernamePasswordToken
     *
     * @throws \Exception
     * @throws \Symfony\Component\Security\Core\Exception\AuthenticationException
     * @throws \InvalidArgumentException
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if (!$userProvider instanceof ApiUserProviderInterface) {
            throw new \InvalidArgumentException(
                sprintf('Instances of "%s" are not supported.', get_class($userProvider))
            );
        }

        try {
            $user = $userProvider->loadUserByUsernameAndPassword(
                $token->getUsername(),
                $token->getCredentials()
            );
        } catch (AuthenticationException $e) {
            throw $e;
        }

        $roles = array();

        foreach ($user->getRoles() as $role) {
            $roleName = strtoupper($role['name']);
            if (!strpos($roleName, 'ROLE_')) {
                array_push($roles, 'ROLE_'.$roleName);
            } else {
                array_push($roles, $roleName);
            }
        }

        return new UsernamePasswordToken($user, $token->getCredentials(), $providerKey, $roles);
    }

    /**
     * @param TokenInterface $token
     * @param $providerKey
     *
     * @return bool
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof UsernamePasswordToken && $providerKey === $token->getProviderKey();
    }
}
