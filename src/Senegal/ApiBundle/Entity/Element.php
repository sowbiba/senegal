<?php

namespace Senegal\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Element
 *
 * @ORM\Table(name="element")
 * @ORM\Entity
 */
class Element
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
     * @var TypeElement
     *
     * @ORM\ManyToOne(targetEntity="TypeElement")
     * @ORM\JoinColumn(name="type_element_id", referencedColumnName="id", nullable=false)
     */
    private $typeElement;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", length=65535, nullable=true)
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="legend", type="text", length=65535, nullable=true)
     */
    private $legend;



    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

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
    public function getLegend()
    {
        return $this->legend;
    }

    /**
     * @param string $legend
     */
    public function setLegend($legend)
    {
        $this->legend = $legend;
    }

    /**
     * @return TypeElement
     */
    public function getTypeElement()
    {
        return $this->typeElement;
    }

    /**
     * @param TypeElement $typeElement
     */
    public function setTypeElement(TypeElement $typeElement)
    {
        $this->typeElement = $typeElement;
    }
}
