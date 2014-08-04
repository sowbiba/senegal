<?php
/**
 * Author: Florent Coquel
 * Date: 02/12/13
 */

namespace Api\Sdk\Model;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\AccessorOrder;
/**
 * Class RevisionFieldSource
 * @package Api\Sdk\Model
 * @ExclusionPolicy("all")
 * @AccessorOrder("custom", custom = {"fieldId", "document" ,"page"})
 */
class RevisionFieldSource extends BaseModel
{

    private $id;

    /**
     * @var integer
     *
     * @Assert\NotNull
     */
    private $revisionId;

    /**
     * @var integer
     * @Expose
     * @Assert\NotNull
     */
    private $fieldId;

    /**
     * @var integer
     * @Assert\NotBlank(message="Veuillez selectionner un document")
     */
    private $documentId;

    /**
     * @var integer
     * @Expose
     * @Assert\NotBlank(message="Veuillez renseigner un numéro de page")
     * @Assert\Type(type="int", message="Le numéro de page doit être un nombre entier")
     * @Assert\Range(min=1, max=999, minMessage="Le numéro de page doit être supérieur à 0", maxMessage = "Le numéro de page doit être inférieur ou égal à 999")
     */
    private $page;

    /**
     * @param mixed $documentId
     */
    public function setDocumentId($documentId)
    {
        $this->documentId = $documentId;
    }

    /**
     * @return mixed
     */
    public function getDocumentId()
    {
        return $this->documentId;
    }

    /**
     * @param mixed $fieldId
     */
    public function setFieldId($fieldId)
    {
        $this->fieldId = $fieldId;
    }

    /**
     * @return mixed
     */
    public function getFieldId()
    {
        return $this->fieldId;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $page
     */
    public function setPage($page)
    {
        if ((int) $page == $page) {
            $page = (int) $page;
        }

        $this->page = $page;
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param mixed $revisionId
     */
    public function setRevisionId($revisionId)
    {
        $this->revisionId = $revisionId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRevisionId()
    {
        return $this->revisionId;
    }

    public function save()
    {
        return $this->sdk->updateFieldSource($this);
    }

    /**
     * @param array $properties
     */
    public function createFromArray(array $properties)
    {
        parent::createFromArray($properties);

        if (isset($properties['page_number'])) {
            $this->page = (int) $properties['page_number'];
        }

        if (isset($properties['document_id'])) {
            $this->documentId = (int) $properties['document_id'];
        }

        if (isset($properties['field_id'])) {
            $this->fieldId = (int) $properties['field_id'];
        }

        if (isset($properties['revision_id'])) {
            $this->revisionId = (int) $properties['revision_id'];
        }
    }

    /**
     * @VirtualProperty
     * @SerializedName("document")
     * @return null|Api\Sdk\Model\Document The source document
     */
    public function getDocument()
    {
        $documentId = $this->getDocumentId();

        return null !== $documentId ? $this->sdk->getMediator()->getColleague("document")->getById($documentId) : null;
    }
    
    public function getPageOffset()
    {
        $document = $this->getDocument();

        return null !== $document ? $document->getPageOffset() : null;
    }
}
