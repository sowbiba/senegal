<?php

/**
 * A field is the unitary container of a contract data.
 *
 * @link https://github.com/Profideo/schoko-backoffice/wiki/Champ
 */

namespace Api\Sdk\Model;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Field
 * @package Api\Sdk\Model
 */
class Field extends BaseModel
{
    const TYPE_TEXT        = 1;
    const TYPE_NUMERIC     = 2;
    const TYPE_LIST        = 3;
    const TYPE_COMPUTED    = 4;
    const TYPE_MULTISELECT = 5;
    const TYPE_DATE        = 6;

    const VALUE_NA = '#NA';
    const VALUE_NC = '#NC';

    private $id;
    private $name;
    // @todo Do we really need to have both chapterId and chapter properties?!
    private $chapterId;
    private $chapter;
    private $typeId;
    private $unit;
    private $displayOrder;
    private $listId;
    private $businessDefiniton;
    private $integrationRule;

    /**
     * Call parent method and use setSourceable method to set isSourceable property
     *
     * @var boolean
     *
     * @Assert\True
     */
    private $isSourceable = false;

    public function createFromArray(array $properties)
    {
        parent::createFromArray($properties);

        if (array_key_exists('isSourceable', $properties)) {
            $this->setSourceable($properties['isSourceable']);
        }
    }

    /**
     * Sets the field's list id
     *
     * @param int $listId
     *
     * @return Field current instance
     */
    public function setListId($listId)
    {
        $this->listId = $listId;

        return $this;
    }

    /**
     * Returns this field's list id
     *
     * @return int
     */
    public function getListId()
    {
        return (int) $this->listId;
    }

    /**
     * Call Sdk and return the array of choices that $this list field has
     *
     * @return mixed
     */
    public function getChoices()
    {
        return $this->sdk->getListChoices($this->getListId());
    }

    /**
     * Gets Field's id
     *
     * @return int
     */
    public function getId()
    {
        return (int) $this->id;
    }

    /**
     *
     * @param  int                  $id
     * @return \Api\Sdk\Model\Field
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets $this field's name (used for Label)
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets $this field's name
     *
     * @param  string               $name
     * @return \Api\Sdk\Model\Field
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns the id of the current field's type
     */
    public function getTypeId()
    {
        return (int) $this->typeId;
    }

    /**
     * Sets $this field's type id
     * @param  int                  $typeId
     * @return \Api\Sdk\Model\Field
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;

        return $this;
    }

    /**
     * Returns the name of $this field's type (typechamps in propel)
     *
     * @return string
     *
     * Calls sdk's method getFieldType already ignored
     * @codeCoverageIgnore
     */
    public function getType()
    {
        return $this->sdk->getFieldType($this->getTypeId());
    }

    /**
     * Returns the Chapter this field belongs to
     *
     * @return \Api\Sdk\Model\Chapter
     *
     * Calls sdk's method getChapter already ignored
     * @codeCoverageIgnore
     */
    public function getChapter()
    {
        $this->chapter = $this->sdk->getChapter($this);

        return $this->chapter;
    }

    /**
     * Returns the Chapter id this field belongs to
     *
     * @return int
     */
    public function getChapterId()
    {
        return $this->chapterId;
    }

    /**
     * Sets $this field's chapter id
     *
     * @param  int                  $chapterId
     * @return \Api\Sdk\Model\Field
     */
    public function setChapterId($chapterId)
    {
        $this->chapterId = $chapterId;

        return $this;
    }

    /**
     * Set $this field's chapter
     *
     * @param  \Api\Sdk\Model\Chapter $chapter
     * @return \Api\Sdk\Model\Field
     */
    public function setChapter($chapter)
    {
        $this->chapter   = $chapter;
        $this->chapterId = $chapter->getId();

        return $this;
    }

    /**
     * Returns $this field's unit
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Sets $this field's unit
     * @param  string               $unit
     * @return \Api\Sdk\Model\Field
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Returns $this display order.
     * (in a chapter, fields are displayed in a certain order, according to this field)
     *
     * @return int
     */
    public function getDisplayOrder()
    {
        return $this->displayOrder;
    }

    /**
     * Sets $this display order
     *
     * @param  int                  $displayOrder
     * @return \Api\Sdk\Model\Field
     */
    public function setDisplayOrder($displayOrder)
    {
        $this->displayOrder = $displayOrder;

        return $this;
    }

    /**
     * Returns the field's sourceable status
     *
     * @return bool
     */
    public function isSourceable()
    {
        return $this->isSourceable;
    }

    /**
     * Sets the field's sourceable status
     *
     * @param $isSourceable
     * @return $this
     */
    public function setSourceable($isSourceable)
    {
        $this->isSourceable = $isSourceable;

        return $this;
    }

    /**
     * Returns true if $this field is of text type
     *
     * @return boolean
     */
    public function isText()
    {
        return static::TYPE_TEXT === $this->typeId;
    }

    /**
     * Returns true if $this field is of numeric type
     *
     * @return boolean
     */
    public function isNumeric()
    {
        return static::TYPE_NUMERIC === $this->typeId;
    }

    /**
     * Returns true if $this field is of list type
     *
     * @return boolean
     */
    public function isList()
    {
        return static::TYPE_LIST === $this->typeId;
    }

    /**
     * Returns true if $this field is of computed type
     *
     * @return boolean
     */
    public function isComputed()
    {
        return static::TYPE_COMPUTED === $this->typeId;
    }

    /**
     * Returns true if $this field is of multiselect type
     *
     * @return boolean
     */
    public function isMultiselect()
    {
        return static::TYPE_MULTISELECT === $this->typeId;
    }

    /**
     * Returns true if $this field is of date type
     *
     * @return boolean
     */
    public function isDate()
    {
        return static::TYPE_DATE === $this->typeId;
    }

    /**
     * Check wether the field is disabled or not
     *
     * @return boolean
     */
    public function isSupported()
    {
        if (in_array($this->getTypeId(), self::getSupportedTypes())) {
            return true;
        }

        return false;
    }

    /**
     * Returns true if this field is source in rule, false otherwise
     *
     * @return bool
     */
    public function isSourceForRules()
    {
        return $this->sdk->isSourceForRules($this);
    }

    /**
     * Returns true if this field is targeted by at least one rule, false otherwise
     *
     * @return bool
     */
    public function isTargetedByRules()
    {
        return $this->sdk->isTargetedByRules($this);
    }

    /**
     * Return the form field mask according to the fieldType
     */
    public function getMask()
    {
        switch ($this->getTypeId()) {
            case static::TYPE_TEXT:
                return 'textarea';

            case static::TYPE_LIST:
            case static::TYPE_MULTISELECT:
                return 'choice';

            case static::TYPE_NUMERIC:
            case static::TYPE_COMPUTED:
            case static::TYPE_DATE:
                return 'text';
        }
    }

    /**
     * Returns the business definition
     *
     * @return string
     */
    public function getBusinessDefinition()
    {
        return $this->businessDefiniton;
    }

    /**
     * Returns the integration rule
     *
     * @return string
     */
    public function getIntegrationRule()
    {
        return $this->integrationRule;
    }

    /**
     * Set a business definition
     *
     * @param string $businessDefiniton
     *
     * @return \Api\Sdk\Model\Field
     */
    public function setBusinessDefinition($businessDefiniton)
    {
        $this->businessDefiniton = $businessDefiniton;

        return $this;
    }

    /**
     * Set an integration rule
     *
     * @param string $integrationRule
     *
     * @return \Api\Sdk\Model\Field
     */
    public function setIntegrationRule($integrationRule)
    {
        $this->integrationRule = $integrationRule;

        return $this;
    }

    /**
     * Returns identifiants of supported field types
     *
     * @return array
     */
    public static function getSupportedTypes()
    {
        return array(
            self::TYPE_TEXT,
            self::TYPE_NUMERIC,
            self::TYPE_LIST,
            self::TYPE_DATE,
        );
    }
}
