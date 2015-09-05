<?php

namespace Senegal\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Block
 *
 * @ORM\Table(name="block", uniqueConstraints={@ORM\UniqueConstraint(name="block_name_unique", columns={"name"})})
 * @ORM\Entity
 */
class Block
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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var TypeBlock
     *
     * @ORM\ManyToOne(targetEntity="TypeBlock")
     * @ORM\JoinColumn(name="type_block_id", referencedColumnName="id", nullable=false)
     */
    private $typeBlock;


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
     * @return TypeBlock
     */
    public function getTypeBlock()
    {
        return $this->typeBlock;
    }

    /**
     * @param TypeBlock $typeBlock
     */
    public function setTypeBlock(TypeBlock $typeBlock)
    {
        $this->typeBlock = $typeBlock;
    }
}
