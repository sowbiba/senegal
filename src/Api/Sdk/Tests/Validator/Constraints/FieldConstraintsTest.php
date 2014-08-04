<?php

namespace Api\Sdk\Tests\Validator\Constraints;

use Api\Sdk\Validator\Constraints\FieldConstraints;
use Api\Sdk\Model\Field;

class FieldConstraintsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FieldConstraints
     */
    private $transformer;

    /**
     * setup
     */
    public function setUp()
    {
        $this->transformer = new FieldConstraints();
    }

    /**
     * @param integer $typeId
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createFieldMock($typeId)
    {
        $mock = $this->getMockBuilder('Api\Sdk\Model\Field')
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue($typeId));

        $mock->expects($this->any())
            ->method('isSourceable')
            ->will($this->returnValue(true));

        $mock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(42));

        return $mock;
    }

    /**
     * Test a date field
     */
    public function testGetConstraintsWithDate()
    {
        $constraints = $this->transformer->getConstraints($this->createFieldMock(Field::TYPE_DATE));

        $this->assertCount(1, $constraints);
        $this->assertSame('Api\Sdk\Validator\Constraints\Date', get_class($constraints[0]));
        $this->assertSame("La valeur saisie n'est pas une date valide pour le champ #42", $constraints[0]->message);
    }

    /**
     * Test a numeric field
     */
    public function testGetConstraintsWithNumeric()
    {
        $constraints = $this->transformer->getConstraints($this->createFieldMock(Field::TYPE_NUMERIC));

        $this->assertCount(1, $constraints);
        $this->assertSame('Api\Sdk\Validator\Constraints\Numeric', get_class($constraints[0]));
        $this->assertSame("La valeur saisie n'est pas une valeur numérique valide pour le champ #42. Le nombre doit être inférieur à 100 milliards, et ne peut avoir que 9 chiffres après la virgule.", $constraints[0]->message);
        $this->assertSame("/^[0-9]{1,11}([.,][0-9]{0,9}){0,1}$/", $constraints[0]->pattern);
    }

    /**
     * Test a list field
     */
    public function testGetConstraintsWithChoice()
    {
        $field = $this->createFieldMock(Field::TYPE_LIST);

        $field->expects($this->once())
            ->method('getChoices')
            ->will($this->returnValue(array(42)));

        $constraints = $this->transformer->getConstraints($field);

        $this->assertCount(1, $constraints);
        $this->assertSame('Api\Sdk\Validator\Constraints\ChoicesList', get_class($constraints[0]));
        $this->assertSame("La valeur saisie n'est pas une valeur de liste valide pour le champ #42", $constraints[0]->message);
        $this->assertSame(array(42), $constraints[0]->choices);
    }

    /**
     * Test an unknown field type
     */
    public function testGetConstraintsWithUnknownType()
    {
        $field = $this->getMockBuilder('Api\Sdk\Model\Field')
            ->disableOriginalConstructor()
            ->getMock();

        $field->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue(0));

        $this->assertCount(0, $this->transformer->getConstraints($field));
    }
}
