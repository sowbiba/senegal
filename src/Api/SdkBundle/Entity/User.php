<?php

namespace Api\SdkBundle\Entity;

/**
 * Api\SdkBundle\Entity\User
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
     * @var Api\SdkBundle\Entity\Role
     */
    protected $roles;

    /**
     * Remove a role
     *
     * @param \Api\SdkBundle\Entity\Role $role
     */
    public function removeRole(Role $role)
    {
        $this->roles->removeElement($role);
    }

    /**
     * Add a role
     *
     * @param \Api\SdkBundle\Entity\Role $role
     */
    public function addRole(Role $role)
    {
        $this->roles[] = $role;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Get roles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRoles()
    {
        return $this->roles;
    }
}
