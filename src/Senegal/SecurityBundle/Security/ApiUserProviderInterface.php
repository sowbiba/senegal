<?php

namespace Senegal\SecurityBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Interface ApiUserProviderInterface.
 *
 * @codeCoverageIgnore
 */
interface ApiUserProviderInterface extends UserProviderInterface
{
    /**
     * @param $username
     * @param $password
     *
     * @return mixed
     */
    public function loadUserByUsernameAndPassword($username, $password);
}
