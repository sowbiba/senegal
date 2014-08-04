<?php
namespace Api\Sdk\Tests\Field;

use Api\Sdk\Tests\SdkTestCase;

class FieldSdkTest extends SdkTestCase
{
    public function testGetById()
    {
        $field = $this->getSdk("field")->getById(1);

        $this->assertInstanceOf('Api\Sdk\Model\Field', $field);
        $this->assertEquals(1, $field->getId());
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testGetIdWithBadParameter()
    {
        $this->getSdk("field")->getById("champ");
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testGetFieldTypeWithBadParameter()
    {
        $this->getSdk("field")->getFieldType("text");
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testGetListChoicesWithBadParameter()
    {
        $this->getSdk("field")->getListChoices("oui/non");
    }
}
