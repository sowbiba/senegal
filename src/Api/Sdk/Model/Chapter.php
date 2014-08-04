<?php

namespace Api\Sdk\Model;

class Chapter extends BaseModel
{
    private $id;
    private $name;
    private $productLineId;
    private $parentId;
    private $children;
    private $fields;
    private $isTable;
    private $level;

    /**
     * Creates a Chapter object from an array of properties.
     * This method overrides BaseModel::createFromArray() because it does special things with children(!).
     *
     * @param array $properties
     *
     * @return \Api\Sdk\Model\Chapter
     */
    public function createFromArray(array $properties)
    {
        $this->children = array();
        $this->fields   = array();

        foreach ($properties as $key => $value) {
            $setMethod = "set" . ucfirst($key);
            if (method_exists($this, $setMethod) && !is_null($value)) {
                if ($setMethod == "setChildren") {
                    foreach ($value as $childId => $childData) {
                        $value[$childId] = new Chapter($this->sdk, $childData);
                    }
                } elseif ($setMethod == "setFields") {
                    $value = $this->sdk->convertFields($value);
                }
                $this->$setMethod($value);
            }
        }

        return $this;
    }

    /**
     * Sets the level (depth) of $this chapter
     *
     * @param int $level
     *
     * @return \Api\Sdk\Model\Chapter
     */
    public function setLevel($level)
    {
        if (!is_int($level)) {
            throw new \BadMethodCallException(__METHOD__ . ' expects an int as parameter, ' . gettype($level) . ' given');
        }

        $this->level = $level;

        return $this;
    }

    /**
     * Gets the level of $this chapter
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set wether $this chapter is a table or not
     *
     * @param bool $isTable
     *
     * @return \Api\Sdk\Model\Chapter
     */
    public function setIsTable($isTable)
    {
        if (!is_bool($isTable)) {
            throw new \BadMethodCallException(__METHOD__ . ' expects a boolean as parameter, ' . gettype($isTable) . ' given');
        }

        $this->isTable = $isTable;

        return $this;
    }

    /**
     * Gets wether $this chapter is a table or not
     *
     * @return bool
     */
    public function getIsTable()
    {
        return $this->isTable;
    }

    /**
     * Sets $this chapter id
     *
     * @param int $id
     *
     * @return \Api\Sdk\Model\Chapter
     */
    public function setId($id)
    {
        if (!is_int($id)) {
            throw new \BadMethodCallException(__METHOD__ . ' expects an int as parameter, ' . gettype($id) . ' given');
        }

        $this->id = $id;

        return $this;
    }

    /**
     * Get $this chapter id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets $this chapter name
     *
     * @param string $name
     *
     * @return \Api\Sdk\Model\Chapter
     */
    public function setName($name)
    {
        if (!is_string($name)) {
            throw new \BadMethodCallException(__METHOD__ . ' expects an int as parameter, ' . gettype($name) . ' given');
        }
        $this->name = $name;

        return $this;
    }

    /**
     * Gets $this chapter name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets $this chapter product line id
     *
     * @param int $productLineId
     *
     * @return \Api\Sdk\Model\Chapter
     */
    public function setProductLineId($productLineId)
    {
        if (!is_int($productLineId)) {
            throw new \BadMethodCallException(__METHOD__ . ' expects an int as parameter, ' . gettype($productLineId) . ' given');
        }

        $this->productLineId = $productLineId;

        return $this;
    }

    /**
     * Gets $this chapter product line id
     *
     * @return int
     */
    public function getProductLineId()
    {
        return $this->productLineId;
    }

    /**
     * Returns the ProductLine object for the current chapter
     *
     * @return ProductLine
     */
    public function getProductLine()
    {
        return $this->sdk->getProductLine($this);
    }

    /**
     * Sets $this chapter parent id
     *
     * @param int $parentId
     *
     * @return \Api\Sdk\Model\Chapter
     *
     * @throws \BadMethodCallException
     */
    public function setParentId($parentId)
    {
        if (!is_int($parentId)) {
            throw new \BadMethodCallException(__METHOD__ . ' expects an int as parameter, ' . gettype($parentId) . ' given');
        }

        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Gets $this chapter parent id
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Sets $this chapter children
     *
     * @param array $children array of \Api\Sdk\Model\Chapter
     *
     * @return \Api\Sdk\Model\Chapter
     */
    public function setChildren(array $children)
    {
        $thisChildren = array();

        foreach ($children as $child) {
            if (!$child instanceof Chapter) {
                throw new \Exception(__METHOD__ . ": Child must be an instance of Api\Sdk\Model\Chapter");
            }
            $thisChildren[] = $child;
        }
        $this->children = $thisChildren;

        return $this;
    }

    /**
     * Gets $this chapter children
     *
     * @return array array of Chapter
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Sets $this chapter fields
     *
     * @param array $fields array of \Api\Sdk\Model\Field
     *
     * @return \Api\Sdk\Model\Chapter
     */
    public function setFields(array $fields)
    {
        $thisFields = array();

        foreach ($fields as $field) {
            if (!$field instanceof Field) {
                throw new \Exception(__METHOD__ . ": Field must be an instance of Api\Sdk\Model\Field");
            }
            $thisFields[] = $field;
        }
        $this->fields = $thisFields;

        return $this;
    }

    /**
     * Gets $this chapter fields
     *
     * @return array array of \Api\Sdk\Model\Field
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Return a table view of current chapte
     *
     * @param array $values
     *
     * For information about structure of chapter view :
     *
     * @link http://doc.si.profideo.com/?p=697#tableau
     *
     * Format of the view:
     *
     * array(
     *     [Field field1, Field field2, ...],
     *     [Chapter subChapter1, [Field field1, ...], [Field field2, ...], ...],
     *     ...
     * )
     *
     *
     * @return array
     */
    public function getTableView(array $values = array(), array $chaptersIdToHide = array())
    {
        $tableView   = array();
        $headerNames = array();
        $rowsIsNa    = array();
        $colsIsNa    = array();
        $subChapters = $this->getChildren();

        // Construct headers table based on fields names
        foreach ($subChapters as $subChapter) {
            foreach ($subChapter->getFields() as $field) {
                if (!in_array(strtolower($field->getName()), $headerNames)) {
                    $tableView[0][] = $field;
                    $headerNames[]  = strtolower($field->getName());
                }
            }
        }

        // Construct rows data which will contains fields
        $rowIndex = 1;

        foreach ($subChapters as $subChapter) {
            // if current subchapter has not fields, do not construct row
            if (!($fields = $subChapter->getFields())) {
                continue;
            }
            $tableView[$rowIndex][] = $subChapter;
            foreach ($fields as $field) {
                $columnIndex = array_search(strtolower($field->getName()), $headerNames) + 1;
                if (false !== ($columnIndex)) {
                    if (!isset ($tableView[$rowIndex][$columnIndex])) {
                        $tableView[$rowIndex][$columnIndex] = array();
                    }
                    if (!isset($colsIsNa[$columnIndex])) {
                        $colsIsNa[$columnIndex] = true;
                    }
                    $tableView[$rowIndex][$columnIndex][] = $field;
                    if (array_key_exists($field->getId(), $values) && Field::VALUE_NA !== $values[$field->getId()]) {
                        $colsIsNa[$columnIndex] = false;
                    }
                }
            }

            $tableView[$rowIndex] = $tableView[$rowIndex] + array_fill(0, count($headerNames) + 1, null);
            ksort($tableView[$rowIndex]);
            $rowsIsNa[$rowIndex] = in_array($subChapter->getId(), $chaptersIdToHide);

            $rowIndex++;
        }
        $tableView["rowsIsNa"] = $rowsIsNa;
        $tableView["colsIsNa"] = $colsIsNa;

        return $tableView;
    }

    /**
     * Returns chapter name
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
