<?php
namespace Api\Sdk\Tests\Model;

use Api\Sdk\Model\Field;
use Api\Sdk\Tests\SdkTestCase;

class FieldTest extends SdkTestCase
{
    private $fields;

    public function setUp()
    {
        $this->fields['text']        = $this->getSdk("field")->getById(1);
        $this->fields['numeric']     = $this->getSdk("field")->getById(2);
        $this->fields['list']        = $this->getSdk("field")->getById(3);
        $this->fields['computed']    = $this->getSdk("field")->getById(4);
        $this->fields['multiselect'] = $this->getSdk("field")->getById(5);
        $this->fields['date']        = $this->getSdk("field")->getById(6);
    }

    /**
     * @dataProvider dataProviderIsSomething
     */
    public function testIsSomething($type, $method)
    {
        $object = $this->fields[$type];
        $this->assertTrue($object->$method());
    }

    public function dataProviderIsSomething()
    {
        return [
            ['text',        'isText'],
            ['numeric',     'isNumeric'],
            ['list',        'isList'],
            ['computed',    'isComputed'],
            ['multiselect', 'isMultiselect'],
            ['date',        'isDate'],
        ];
    }

    public function testCreateFromArray()
    {
        $fieldData = [
            'id'                 => 1,
            'name'               => 'Some field',
            'chapterId'          => 499,
            'typeId'             => 1,
            'unit'               => 'kilometer per hour',
            'displayOrder'       => 43,
            'isSourceable'       => true,
            'businessDefinition' => 'Test business definition',
            'integrationRule'    => 'Test integration rule'

        ];

        // Create a fake contract for testing
        $field = new Field($this->getSdk("field"), $fieldData);

        // Assert
        $this->assertEquals($field->getId(),                 $fieldData['id']);
        $this->assertEquals($field->getName(),               $fieldData['name']);
        $this->assertEquals($field->getChapterId(),          $fieldData['chapterId']);
        $this->assertEquals($field->getTypeId(),             $fieldData['typeId']);
        $this->assertEquals($field->getUnit(),               $fieldData['unit']);
        $this->assertEquals($field->getDisplayOrder(),       $fieldData['displayOrder']);
        $this->assertEquals($field->isSourceable(),          $fieldData['isSourceable']);
        $this->assertEquals($field->getBusinessDefinition(), $fieldData['businessDefinition']);
        $this->assertEquals($field->getIntegrationRule(),    $fieldData['integrationRule']);
    }

    public function dataProviderReturnsTargetedFieldsByRules()
    {
        $data = [[24],[45]];

        return $data;
    }

    public function dataProviderReturnsSourceFieldsForRules()
    {
        $data = [[30],[31],[32]];

        return $data;
    }

    /**
     * @dataProvider dataProviderReturnsTargetedFieldsByRules
     */
    public function testIsTargetedByRulesReturnTrue($fieldId)
    {
        $field = $this->getSdk("field")->getById($fieldId);

        $this->assertTrue($field->isTargetedByRules());
    }

    /**
     * @dataProvider dataProviderReturnsSourceFieldsForRules
     */
    public function testIsTargetedByRulesReturnFalse($fieldId)
    {
        $field = $this->getSdk("field")->getById($fieldId);

        $this->assertFalse($field->isTargetedByRules());
    }

    /**
     * @dataProvider dataProviderReturnsSourceFieldsForRules
     */
    public function testIsSourceForRulesReturnTrue($fieldId)
    {
        $field = $this->getSdk("field")->getById($fieldId);

        $this->assertTrue($field->isSourceForRules());
    }

    /**
     * @dataProvider dataProviderReturnsTargetedFieldsByRules
     */
    public function testIsSourceForRulesReturnFalse($fieldId)
    {
        $field = $this->getSdk("field")->getById($fieldId);

        $this->assertFalse($field->isSourceForRules());
    }
}
