<?php

namespace Senegal\Api\SdkBundle\Entity;

/**
 * Senegal\Api\SdkBundle\Entity\User
 *
 * @codeCoverageIgnore
 */
class User extends BaseEntity
{
    /**
     *
     * @var int
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $username;

    /**
     *
     * @var Senegal\Api\SdkBundle\Entity\Role
     */
    protected $roles;

    /**
     * Remove a role
     *
     * @param \Senegal\Api\SdkBundle\Entity\Role $role
     */
    public function removeRole(Role $role)
    {
        $this->roles->removeElement($role);
    }

    /**
     * Add a role
     *
     * @param \Senegal\Api\SdkBundle\Entity\Role $role
     */
    public function addRole(Role $role)
    {
        $this->roles[] = $role;
    }
}
