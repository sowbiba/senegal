<?php

namespace Senegal\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * Forfait
 *
 * @ORM\EntityListeners({"Senegal\ApiBundle\Listener\Entity\ForfaitListener"})
 *
 * @ORM\Table(name="forfait", uniqueConstraints={@ORM\UniqueConstraint(name="forfait_name_unique", columns={"name"})})
 * @ORM\Entity(repositoryClass="Senegal\ApiBundle\Repository\ForfaitRepository")
 * @UniqueEntity(fields="name",message="forfait.fields.duplicate.name")
 */
class Forfait
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
     *      "api_forfait_list",
     *      "api_forfait_read",
     *      "api_forfait_update",
     *      "back_forfait_list",
     *      "back_forfait_read",
     *      "back_forfait_update",
     *      "front_user_create"
     * })
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     *
     * @Assert\NotBlank(
     *      message="forfait.fields.empty.name"
     * )
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "api_forfait_list",
     *      "api_forfait_read",
     *      "api_forfait_update",
     *      "back_forfait_list",
     *      "back_forfait_read",
     *      "back_forfait_update",
     *      "front_user_create"
     * })
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="ForfaitHasTypePage", mappedBy="forfait", cascade={"persist","remove", "merge"})
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "api_forfait_list",
     *      "api_forfait_read",
     *      "api_forfait_update",
     *      "back_forfait_list",
     *      "back_forfait_read",
     *      "back_forfait_update"
     * })
     */
    private $forfaitTypePages;



    public function __construct()
    {
        $this->forfaitTypePages = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Forfait
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    public function getForfaitTypePages()
    {
        return $this->forfaitTypePages;
    }

    public function addForfaitTypePage(ForfaitHasTypePage $forfaitTypePage)
    {
        if (!$this->forfaitTypePages->contains($forfaitTypePage)) {
            $this->forfaitTypePages->add($forfaitTypePage);
        }
    }

    public function setForfaitTypePages(array $forfaitTypePages)
    {
        $this->forfaitTypePages = new ArrayCollection();

        foreach($forfaitTypePages as $forfaitTypePage) {
            $this->addForfaitTypePage($forfaitTypePage);
        }

        return $this;
    }

    public function removeForfaitTypePage(ForfaitHasTypePage $forfaitTypePage)
    {
        if ($this->forfaitTypePages->contains($forfaitTypePage)) {
            $this->forfaitTypePages->removeElement($forfaitTypePage);
        }
    }
}
