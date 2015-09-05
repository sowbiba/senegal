<?php

namespace Senegal\ApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Senegal\ApiBundle\Utils\HashGenerator;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\EntityListeners({"Senegal\ApiBundle\Listener\Entity\UserListener"})
 *
 * @ORM\Table(name="user", uniqueConstraints={@ORM\UniqueConstraint(name="user_username_unique", columns={"username"})})
 * @ORM\Entity(repositoryClass="Senegal\ApiBundle\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "authentication",
     *      "user_list",
     *      "user_create",
     *      "user_read",
     *      "user_update"
     * })
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, nullable=false)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "authentication",
     *      "user_list",
     *      "user_create",
     *      "user_read",
     *      "user_update"
     * })
     *
     * @Assert\NotBlank(
     *      message="user.fields.empty.username"
     * )
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=128, nullable=false)
     *
     * @Assert\NotBlank(
     *      message="user.fields.empty.password"
     * )
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="algorithm", type="string", length=128)
     */
    private $algorithm;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "user_list",
     *      "user_create",
     *      "user_read",
     *      "user_update"
     * })
     */
    private $active;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=128)
     */
    private $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255, nullable=true)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "user_list",
     *      "user_create",
     *      "user_read",
     *      "user_update"
     * })
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "user_list",
     *      "user_create",
     *      "user_read",
     *      "user_update"
     * })
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text", length=65535, nullable=true)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "user_edit",
     *      "user_read"
     * })
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=64, nullable=true)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "user_create",
     *      "user_read",
     *      "user_update"
     * })
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=512, nullable=true)
     *
     * @Assert\NotBlank(
     *      message="user.fields.empty.email"
     * )
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "user_list",
     *      "user_create",
     *      "user_read",
     *      "user_update"
     * })
     */
    private $email;

    /**
     * @var Role
     *
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id", nullable=false)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "user_list"
     * })
     *
     * @Assert\NotNull(
     *      message="user.fields.empty.role"
     * )
     */
    private $role;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     *
     * @Gedmo\Timestampable(on="create")
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "user_list",
     *      "user_create",
     *      "user_read",
     *      "user_update"
     * })
     */
    private $createdAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     *
     * @Gedmo\Blameable(on="create")
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     *
     * @Gedmo\Timestampable(on="change", field={"username", "active", "password", "roles", "profile"})
     */
    private $updatedAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     *
     * @Gedmo\Blameable(on="change", field={"username", "active", "password", "roles", "profile"})
     */
    private $updatedBy;


    public function __construct()
    {
        $this->salt = HashGenerator::generate();
        $this->algorithm = 'sha1';
        $this->active = false;
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
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->getActive();
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $password = hash($this->algorithm, $this->salt.strtolower($password));

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
     * @param string $algorithm
     */
    public function setAlgorithm($algorithm)
    {
        $this->algorithm = $algorithm;
    }

    /**
     * @return string
     */
    public function getAlgorithm()
    {
        return $this->algorithm;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @return int
     *
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("role")
     * @Serializer\Groups({
     *      "user_create",
     *      "user_read",
     *      "user_update"
     * })
     */
    public function getRoleId()
    {
        return $this->getRole()->getId();
    }

    public function setRole(Role $role)
    {
        $this->role = $role;
    }

    /**
     * @return Role
     */
    public function getRole()
    {
        return $this->role;
    }

    public function getRoleName()
    {
        return $this->getRole()->getName();
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @return string
     *
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("name")
     * @Serializer\Groups({"authentication", "user_list", "user_create", "user_read", "user_update"})
     */
    public function getName()
    {
        return $this->getLastname();
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return User
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdBy
     *
     * @param User $user
     * @return User
     */
    public function setCreatedBy(User $user)
    {
        $this->createdBy = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return User
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set updatedBy
     *
     * @param User $user
     * @return User
     */
    public function setUpdatedBy(User $user)
    {
        $this->updatedBy = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     *
     * @codeCoverageIgnore
     */
    public function eraseCredentials()
    {
    }

    public function getRoles()
    {
        $roles = new ArrayCollection();

        if(null !== $this->getRole()) {
            $roles->add($this->getRole());
        }

        return $roles;
    }
}
