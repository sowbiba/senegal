<?php

namespace Senegal\SecurityBundle\Security;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ApiUserProvider.
 *
 * @codeCoverageIgnore
 */
class ApiUserProvider implements ApiUserProviderInterface
{
    private $client;
    private $container;

    public function __construct(ClientInterface $client, ContainerInterface $container)
    {
        $this->client = $client;
        $this->container = $container;
    }

    /**
     * @param $username
     * @param $password
     *
     * @return mixed
     *
     * @throws AuthenticationException
     */
    public function loadUserByUsernameAndPassword($username, $password)
    {
        try {
            $response = $this->client->post($this->container->getParameter('api_login_action'),
                [
                    'body' => [
                        'login' => $username,
                        'password' => $password,
                        'environment' => 'back',
                    ],
                ]
            );
        } catch (BadResponseException $e) {
            //var_dump($password, $e->getMessage());die();
            throw new AuthenticationException('form.login.invalid_username_or_password');
        }

        return new User($response->json());
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        throw new \RuntimeException('loadUserByUsername not supported by the API');
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $class === 'Senegal\SecurityBundle\Security\User';
    }
}
