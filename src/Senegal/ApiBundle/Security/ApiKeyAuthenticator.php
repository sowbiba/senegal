<?php

namespace Senegal\ApiBundle\Security;

use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

class ApiKeyAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
    protected $userProvider;

    protected $httpUtils;

    public function __construct(ApiKeyUserProvider $userProvider, HttpUtils $httpUtils)
    {
        $this->userProvider = $userProvider;
        $this->httpUtils = $httpUtils;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response('authentication.failed', Response::HTTP_FORBIDDEN);
    }

    public function createToken(Request $request, $providerKey)
    {
        return new PreAuthenticatedToken('anon.', $request->headers->get('api-key', 'anonymous'), $providerKey);
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $apiKey = $token->getCredentials();

        if ('anonymous' === $apiKey) {
            return new AnonymousToken('anon.', 'anonymous', ['IS_AUTHENTICATED_ANONYMOUSLY']);
        }

        $username = $this->userProvider->getUsernameForApiKey($apiKey);

        if (!$username) {
            throw new AuthenticationException(sprintf('API Key "%s" does not exist.', $apiKey));
        }

        $user = $this->userProvider->loadUserByUsername($username);

        return new PreAuthenticatedToken(
            $user,
            $apiKey,
            $providerKey,
            ['back_admin']//$user->getRolesNames()
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }
}
