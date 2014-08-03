<?php

namespace Senegal\Api\SdkBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Senegal\Api\SdkBundle\Entity\DocumentType
 *
 * @codeCoverageIgnore
 */
class DocumentType extends BaseEntity
{
    protected $name;
    protected $documents;

    public function __construct()
    {
        parent::__construct();

        $this->documents = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function setDocuments($documents)
    {
        $this->documents = $documents;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    public function getDocuments()
    {
        return $this->documents;
    }
}
