<?php

namespace Senegal\Api\SdkBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Senegal\Api\SdkBundle\Entity\Document
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
    protected $pageOffset;
    protected $releasedAt;
    protected $updatedBy;
    protected $contracts;
    protected $revisions;

    public function __construct()
    {
        $this->revisions  = new ArrayCollection();
        $this->contracts  = new ArrayCollection();
        $this->createdBy  = 1;
        $this->uploadedBy = 1;
        $this->updatedBy  = 1;
    }

    /**
     * @param string $description
     *
     * @return Senegal\Api\SdkBundle\Entity\Document
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
     * @return Senegal\Api\SdkBundle\Entity\Document
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
     * @return Senegal\Api\SdkBundle\Entity\Document
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * @param  string                        $reference
     * @return Senegal\Api\SdkBundle\Entity\Document
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
     * @return Senegal\Api\SdkBundle\Entity\Document
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
     * @return Senegal\Api\SdkBundle\Entity\Document
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
     * Sets Document's page offset
     *
     * @param  int                           $pageOffset
     * @return Senegal\Api\SdkBundle\Entity\Document
     */
    public function setPageOffset($pageOffset)
    {
        $this->pageOffset = $pageOffset;

        return $this;
    }

    /**
     * Returns page offset of the document
     *
     * @return int
     */
    public function getPageOffset()
    {
        return $this->pageOffset;
    }

    /**
     * @param  Senegal\Api\SdkBundle\Entity\DocumentType $type
     * @return Senegal\Api\SdkBundle\Entity\Document
     */
    public function setType(DocumentType $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Senegal\Api\SdkBundle\Entity\DocumentType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param  int                           $createdBy user id
     * @return Senegal\Api\SdkBundle\Entity\Document
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
     * @param  $revisions
     * @return Senegal\Api\SdkBundle\Entity\Document
     */
    public function setRevisions($revisions)
    {
        $this->revisions = $revisions;

        return $this;
    }

    /**
     * @param  int                           $updatedBy user id
     * @return Senegal\Api\SdkBundle\Entity\Document
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
     * @return mixed
     */
    public function getRevisions()
    {
        return $this->revisions;
    }

    /**
     * @param  Revision                      $revision
     * @return Senegal\Api\SdkBundle\Entity\Document
     */
    public function addRevision(Revision $revision)
    {
        $this->revisions[] = $revision;

        return $this;
    }

    /**
     * @param  mixed                         $contract
     * @return Senegal\Api\SdkBundle\Entity\Document
     */
    public function addContract(Contract $contract)
    {
        $this->contracts[] = $contract;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContracts()
    {
        return $this->contracts;
    }
}
