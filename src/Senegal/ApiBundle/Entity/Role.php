<?php

namespace Senegal\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Table(name="role")
 * @ORM\Entity()
 *
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\AccessorOrder("alphabetical")
 */
class Role
{
    const SUPER_ADMIN_ROLE = 'SUPER_ADMIN';
    const SUPER_ADMIN_ROLE_ID = 1;

    const ACCOUNT_ADMIN_ROLE = 'ACCOUNT_ADMIN';
    const ACCOUNT_ADMIN_ROLE_ID = 2;

    const USER_ROLE = 'USER';
    const USER_ROLE_ID = 3;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "authentication",
     *      "role_list",
     *      "user_create",
     *      "user_read",
     *      "user_update"
     * })
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "authentication",
     *      "role_list",
     *      "user_create",
     *      "user_list",
     *      "user_read",
     *      "user_update"
     * })
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "role_list"
     * })
     */
    protected $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }

    /**
     * @param integer $id
     *
     * @return Role
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     *
     * @return Role
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $description
     *
     * @return Role
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
