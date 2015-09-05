<?php

namespace Senegal\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * TypePage
 *
 * @ORM\EntityListeners({"Senegal\ApiBundle\Listener\Entity\TypePageListener"})
 *
 * @ORM\Table(name="type_page", uniqueConstraints={@ORM\UniqueConstraint(name="type_page_name_unique", columns={"name"})})
 * @ORM\Entity(repositoryClass="Senegal\ApiBundle\Repository\TypePageRepository")
 * @UniqueEntity(fields="name",message="type_pages.fields.duplicate.name")
 */
class TypePage
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
     *      "forfait_list",
     *      "forfait_read",
     *      "forfait_update",
     *      "type_page_list"
     * })
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "forfait_list",
     *      "type_page_list",
     *      "type_page_read",
     *      "type_page_update"
     * })
     */
    private $name;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Page", mappedBy="typePage")
     */
    private $pages;



    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return ArrayCollection
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @param ArrayCollection $pages
     */
    public function setPages($pages)
    {
        $this->pages = $pages;
    }
}
