<?php

namespace Api\Sdk\Model;

use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\AccessorOrder;

/**
 * Class User
 * @ExclusionPolicy("all")
 * @AccessorOrder("custom", custom ={"id", "lastname", "firstname", "email", "company", "roles"})
 */
class User extends BaseModel implements UserInterface, EquatableInterface, \Serializable
{
    const TYPE_CLIENT = 1;
    const TYPE_INTERNAL = 2;
    const TYPE_TECHNICAL = 3;

    /**
     * @var int
     * @Expose
     */
    protected $id;

    /**
     * @var string
     * @Expose
     */
    protected $firstname;

    /**
     * @var string
     * @Expose
     */
    protected $lastname;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     * @Expose
     */
    protected $email;

    /**
     * @var string
     */
    protected $salt;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var array
     * @expose
     */
    protected $roles = array();

    /**
     * @var integer
     */
    protected $type;

    /**
     * @var string
     * @Expose
     */
    protected $company;

    /**
     * @var boolean
     */
    protected $active;

    /**
     * Set fullName of user.
     *
     * @return mixed
     */
    public function __toString()
    {
        return sprintf("%s %s", $this->firstname, $this->lastname);
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param array $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return array_unique($this->roles);
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @return bool
     */
    public function isClient()
    {
        return $this->getType() === static::TYPE_CLIENT;
    }

    /**
     * Check if it is an internal user
     *
     * @return bool
     */
    public function isInternal()
    {
        return $this->getType() === static::TYPE_INTERNAL;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials() {}

    /**
     * The equality comparison should neither be done by referential equality
     * nor by comparing identities (i.e. getId() === getId()).
     *
     * However, you do not need to compare every attribute, but only those that
     * are relevant for assessing whether re-authentication is required.
     *
     * Also implementation should consider that $user instance may implement
     * the extended user interface `AdvancedUserInterface`.
     *
     * @param UserInterface $user
     *
     * @return Boolean
     */
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->getSalt() !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->salt,
            $this->password,
        ));
    }

    /**
     * @param string $data
     */
    public function unserialize($data)
    {
        list(
            $this->id,
            $this->username,
            $this->salt,
            $this->password
        ) = unserialize($data);
    }

    /**
     * @see sfGuardUser::getBackofficeRole
     *
     * @return mixed
     */
    public function getBackOfficeRole()
    {
        return $this->sdk->getBackOfficeRole($this);
    }

    /**
     * @see sfGuardUser
     * @return mixed
     */
    public function hasLtaCredentials()
    {
        return $this->sdk->hasLtaCredentials($this);
    }

    /**
     * @see sfGuardUser
     * @return mixed
     */
    public function hasDistribCredentials()
    {
        return $this->sdk->hasDistribCredentials($this);
    }

    /**
     * @see sfGuardUser
     * @return mixed
     */
    public function hasLtaInvoice()
    {
        return $this->sdk->hasLtaInvoice($this);
    }

    /**
     * @see sfGuardUser
     * @return mixed
     */
    public function hasDistribInvoice()
    {
        return $this->sdk->hasDistribInvoice($this);
    }
}
