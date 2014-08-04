<?php
namespace Api\Sdk\Model;

class DocumentType extends BaseModel
{
    private $id;
    private $name;

    /**
     * Set DocumentType's id
     *
     * @param int $id
     *
     * @return DocumentType current instance
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set DocumentType's name
     *
     * @param string $name
     *
     * @return DocumentType current instance
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get DocumentType's id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get DocumentType's name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getName();
    }
}
