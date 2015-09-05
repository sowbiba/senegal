<?php

namespace Senegal\ApiBundle\Security;

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class PasswordEncoder extends MessageDigestPasswordEncoder
{
    public function __construct()
    {
        parent::__construct('sha1', false, 1);
    }

    /**
     * Merges a password and a salt.
     *
     * @param string $password the password to be used
     * @param string $salt     the salt to be used
     *
     * @return string a merged password and salt
     */
    protected function mergePasswordAndSalt($password, $salt)
    {
        return $salt.strtolower($password);
    }
}
