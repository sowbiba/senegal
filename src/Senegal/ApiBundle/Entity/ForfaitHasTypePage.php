<?php

namespace Senegal\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * ForfaitHasTypePage
 *
 * @ORM\Table(name="forfait_has_type_page")
 * @ORM\Entity(repositoryClass="Senegal\ApiBundle\Repository\ForfaitHasTypePageRepository")
 */
class ForfaitHasTypePage
{
    /**
     * @var Forfait
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Forfait", inversedBy="forfaitTypePages")
     * @ORM\JoinColumn(name="forfait_id", referencedColumnName="id", nullable=false)
     */
    private $forfait;

    /**
     * @var TypePage
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="TypePage")
     * @ORM\JoinColumn(name="type_page_id", referencedColumnName="id", nullable=false)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "forfait_list"
     * })
     */
    private $typePage;

    /**
     * @var integer
     *
     * @ORM\Column(name="allowed_page_number", type="integer", nullable=true)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "forfait_list",
     *      "forfait_read",
     *      "forfait_update"
     * })
     */
    private $allowedPageNumber;

    /**
     * @return int
     */
    public function getAllowedPageNumber()
    {
        return $this->allowedPageNumber;
    }

    /**
     * @param int $allowedPageNumber
     */
    public function setAllowedPageNumber($allowedPageNumber)
    {
        $this->allowedPageNumber = $allowedPageNumber;
    }

    /**
     * @return Forfait
     */
    public function getForfait()
    {
        return $this->forfait;
    }

    /**
     * @param Forfait $forfait
     */
    public function setForfait(Forfait $forfait)
    {
        $this->forfait = $forfait;
    }

    /**
     * @return TypePage
     */
    public function getTypePage()
    {
        return $this->typePage;
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("typePage")
     * @Serializer\Groups({
     *      "forfait_create",
     *      "forfait_read",
     *      "forfait_update"
     * })
     *
     * @return int
     */
    public function getTypePageId()
    {
        return $this->typePage->getId();
    }

    /**
     * @param TypePage $typePage
     */
    public function setTypePage(TypePage $typePage)
    {
        $this->typePage = $typePage;
    }
}
