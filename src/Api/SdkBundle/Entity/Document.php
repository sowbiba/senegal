<?php

namespace Api\SdkBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Api\SdkBundle\Entity\Document
 *
 * @codeCoverageIgnore
 */
class Document extends BaseEntity
{
    protected $file;
    protected $filePath;
    protected $type;
    protected $reference;
    protected $description;
    protected $createdBy;
    protected $size;
    protected $releasedAt;
    protected $updatedBy;

    public function __construct()
    {
        $this->createdBy  = 1;
        $this->uploadedBy = 1;
        $this->updatedBy  = 1;
    }

    /**
     * @param string $description
     *
     * @return Api\SdkBundle\Entity\Document
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
     * @param  Symfony\Component\HttpFoundation\File $file
     * @return Api\SdkBundle\Entity\Document
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return Symfony\Component\HttpFoundation\File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Returns the file path
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Sets the file path
     *
     * @param  string                        $filePath
     * @return Api\SdkBundle\Entity\Document
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * @param  string                        $reference
     * @return Api\SdkBundle\Entity\Document
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param  \DateTime                     $releasedAt
     * @return Api\SdkBundle\Entity\Document
     */
    public function setReleasedAt($releasedAt)
    {
        $this->releasedAt = $releasedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getReleasedAt()
    {
        return $this->releasedAt;
    }

    /**
     * @param  int                           $size
     * @return Api\SdkBundle\Entity\Document
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }
    
    /**
     * @param  Api\SdkBundle\Entity\DocumentType $type
     * @return Api\SdkBundle\Entity\Document
     */
    public function setType(DocumentType $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Api\SdkBundle\Entity\DocumentType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param  int                           $createdBy user id
     * @return Api\SdkBundle\Entity\Document
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return int user id
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
    
    /**
     * @param  int                           $updatedBy user id
     * @return Api\SdkBundle\Entity\Document
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * @return int user id
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;


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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Document
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Document
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}