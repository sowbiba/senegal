<?php
namespace Api\Sdk\Model;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Api\Sdk\Validator\Constraints as ApiAssert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * A document is a file containing a copy of an information used by an analyst to fill some business data(!)
 *
 * @link https://github.com/Profideo/schoko-backoffice/wiki/Document
 */

/**
 * A document is a file containing a copy of an information used by an analyst to fill some business data(!)
 *
 * @link https://github.com/Profideo/schoko-backoffice/wiki/Document
 *
 * Class Document
 * @package Api\Sdk\Model
 *
 * @ExclusionPolicy("all")
 *
 */
class Document extends BaseModel implements UploadableInterface, TimestampableInterface, BlameableInterface
{
    /**
     * @var int
     * @Expose
     */
    protected $id;

    /**
     * @Assert\NotBlank(message = "Veuillez spécifier la nature du document")
     * @Expose
     */
    protected $type;
    /**
     * @var string
     * @Expose
     */
    protected $reference;
    /**
     * @var string
     * @Expose
     */
    protected $description;

    /**
     * @var Symfony\Component\HttpFoundation\File\UploadedFile
     *
     * @Assert\NotBlank(groups={"create"}, message = "Veuillez fournir un fichier au format PDF")
     * @Assert\File(
     *     maxSize = "10000k",
     *     maxSizeMessage = "Veuillez fournir un fichier inférieur à 10MB",
     *     mimeTypes        = {"application/pdf", "application/x-pdf"},
     *     mimeTypesMessage = "Veuillez fournir un fichier au format PDF"
     * )
     * @ApiAssert\ExistingFilename
     */
    protected $file;

    /**
     * @var string
     * @Expose
     */
    protected $filePath;
    /**
     * @var string
     * @Expose
     */
    protected $fileName;
    /**
     * @var int
     * @Assert\Type(type="int", message="Le décalage doit être un nombre entier. Merci de ne pas mettre de signe si le décalage est positif.")
     * @Assert\Range(min=-999, max=999, minMessage="Le numéro de page doit être supérieur à -999", maxMessage = "Le numéro de page doit être inférieur ou égal à 999")
     * @Expose
     */
    protected $pageOffset;
    protected $size;
    protected $createdAt;
    protected $updatedAt;

    /**
     * @Assert\Date(message = "Veuillez entrer une date valide")
     * @Expose
     */
    protected $releasedAt;

    /**
     * @var UserInterface
     */
    protected $createdBy;

    /**
     * @var UserInterface
     */
    protected $updatedBy;

    /**
     * Sets Document's id
     *
     * @param int $id
     *
     * @return Document current instance
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set values in properties of current instance.
     *
     * @param array $properties array of properties and values to set (@example : ['id' => 1, 'name' => 'Contract #1'])
     *
     * @todo Should we lazy load the relation ?
     */
    public function createFromArray(array $properties)
    {
        if (isset($properties['type'])) {
            $this->setType($this->sdk->getType($properties['type']['id']));
            unset($properties['type']);
        }

        if (isset($properties['createdBy']) && !empty($properties['createdBy'])) {
            $this->setCreatedBy($this->sdk->getUser($properties['createdBy']));
            unset($properties['createdBy']);
        }

        if (isset($properties['updatedBy']) && !empty($properties['updatedBy'])) {
            $this->setUpdatedBy($this->sdk->getUser($properties['updatedBy']));
            unset($properties['updatedBy']);
        }

        parent::createFromArray($properties);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = parent::toArray();
        $data['type'] = $this->getType()->getId();
        $data['createdBy'] = $this->getCreatedBy() ? $this->getCreatedBy()->getId() : null;
        $data['updatedBy'] = $this->getUpdatedBy() ? $this->getUpdatedBy()->getId() : null;
        $data['createdAt'] = $this->getCreatedAt();
        $data['updatedAt'] = $this->getUpdatedAt();
        $data['releasedAt'] = $this->getReleasedAt();

        return $data;
    }

    /**
     * Sets document's type with array of DocumentType's data
     *
     * @param DocumentType $type
     *
     * @return Document current instance
     */
    public function setType(DocumentType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Sets Document's reference
     *
     * @param string $reference
     *
     * @return Document current instance
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Sets Document's description
     *
     * @param string $description
     *
     * @return Document current instance
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Sets Document's file
     *
     * @param Symfony\Component\HttpFoundation\File $file
     *
     * @return Document current instance
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Sets Document's file name
     *
     * @param string $fileName
     *
     * @return Document current instance
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Sets Document's file path
     *
     * @param string $filePath
     *
     * @return Document current instance
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        // When setting filePath, also set fileName
        $this->setFileName(basename($filePath));

        return $this;
    }

    /**
     * Sets Document's size in bytes (= octets)
     *
     * @param int $size
     *
     * @return Document current instance
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Sets Document's page offset
     *
     * @param int $pageOffset
     *
     * @return Document current instance
     */
    public function setPageOffset($pageOffset)
    {
        $this->pageOffset = $pageOffset;

        return $this;
    }

    /**
     * Sets Document's creation date
     *
     * @param \DateTime $createdAt
     *
     * @return Document current instance
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Sets Document's update date
     *
     * @param \DateTime $updatedAt
     *
     * @return Document current instance
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Sets Document's release date
     *
     * @param \DateTime $releasedAt
     *
     * @return Document current instance
     */
    public function setReleasedAt($releasedAt)
    {
        $this->releasedAt = $releasedAt;

        return $this;
    }

    /**
     * Sets Document's creator
     *
     * @param UserInterface $createdBy
     *
     * @return Document current instance
     */
    public function setCreatedBy(UserInterface $createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Sets Document's editor
     *
     * @param UserInterface $updatedBy
     *
     * @return Document current instance
     */
    public function setUpdatedBy(UserInterface $updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Gets Document's id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets Document's type
     *
     * @return DocumentType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Gets Document's reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Gets Document's description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Gets Document's file
     *
     * @return Symfony\Component\HttpFoundation\File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Gets Document's fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Gets Document's filePath
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Gets Document's size in bytes (= octets)
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Gets Document's page offset
     *
     * @return int
     */
    public function getPageOffset()
    {
        return $this->pageOffset;
    }

    /**
     * Gets Document's creation date
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Gets Document's update date
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Gets Document's release date
     *
     * @return \DateTime
     */
    public function getReleasedAt()
    {
        return $this->releasedAt;
    }

    /**
     * Gets Document's creator id
     *
     * @return int
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Gets Document's editor id
     *
     * @return int
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Returns wether $this document belongs to a revision, or not
     *
     * @return bool
     *
     * Need a database
     * @codeCoverageIgnore
     */
    public function belongsToRevisions()
    {
        return $this->sdk->belongsToRevisions($this->getId());
    }

    /**
     * Returns wether $this document can be deleted, or not
     *
     * @return bool
     *
     * Need a database
     * @codeCoverageIgnore
     */
    public function canBeDeleted()
    {
        return false == $this->belongsToRevisions();
    }

    /**
     * Returns revisions containing the current document
     *
     * @return Api\Sdk\Model\Revision[]
     */
    public function getRevisions()
    {
        return $this->sdk->getRevisions($this);
    }
    /**
     * Returns revisions grouped by contract
     * @return array with id => revisions
     */
    public function getRevisionsGroupedByContract()
    {
        $documentRevisions = $this->getRevisions();

        $revisions = array();

        foreach($documentRevisions as $revision) {
           $revisions[$revision->getContract()->getId()][] = $revision;
        }

        return $revisions;
    }

    public function delete()
    {
        return $this->sdk->delete($this);
    }
}
