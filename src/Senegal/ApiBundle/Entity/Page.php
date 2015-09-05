<?php

namespace Senegal\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Page
 *
 * @ORM\Table(name="page", uniqueConstraints={@ORM\UniqueConstraint(name="page_slug_unique", columns={"slug"})})
 * @ORM\Entity
 */
class Page
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, nullable=false)
     */
    private $slug;

    /**
     * @var TypePage
     *
     * @ORM\ManyToOne(targetEntity="TypePage")
     * @ORM\JoinColumn(name="type_page_id", referencedColumnName="id", nullable=false)
     */
    private $typePage;

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
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return TypePage
     */
    public function getTypePage()
    {
        return $this->typePage;
    }

    /**
     * @param TypePage $typePage
     */
    public function setTypePage(TypePage $typePage)
    {
        $this->typePage = $typePage;
    }
}
