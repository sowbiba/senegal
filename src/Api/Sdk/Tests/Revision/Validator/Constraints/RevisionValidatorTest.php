<?php
/**
 * Author: Florent Coquel
 * Date: 26/11/13
 */

namespace Api\Sdk\Tests\Revision\Validator\Constraints;

use Api\Sdk\Model\Field;
use Api\Sdk\Model\RevisionFieldSource;
use Api\SdkBundle\Validator\Constraints\RevisionConstraint;
use Api\Sdk\Revision\Validator\Constraints\RevisionValidator;
use Api\Sdk\Tests\SdkTestCase;

class RevisionValidatorTest extends SdkTestCase
{

    protected $context;
    protected $isValidator;
    protected $connectorMock;
    protected $revisionMock;
    protected $contractMock;
    protected $productLineMock;
    protected $fieldMock;

    protected function setUp()
    {
        $this->context = $this->getMock('Symfony\Component\Validator\ExecutionContext', array(), array(), '', false);

        $this->connectorMock = $this->getMockBuilder('Api\Sdk\ProductLine\Connector\Propel\ProductLinePropelConnector')
            ->disableOriginalConstructor()
            ->getMock();

        $this->revisionMock = $this->getMockBuilder('Api\Sdk\Model\Revision')
            ->disableOriginalConstructor()
            ->getMock();

        $this->contractMock = $this->getMockBuilder('Api\Sdk\Model\Contract')
            ->disableOriginalConstructor()
            ->getMock();

        // Mock model ProductLine
        $this->productLineMock = $this->getMockBuilder('Api\Sdk\Model\ProductLine')
            ->disableOriginalConstructor()
            ->getMock();

        // Mock entity Field
        $this->fieldMock = $this->getMockBuilder('Api\Sdk\Model\Field')
            ->disableOriginalConstructor()
            ->getMock();

        $this->validator = new RevisionValidator($this->connectorMock);
        $this->validator->initialize($this->context);
    }

    protected function tearDown()
    {
        $this->context      = null;
        $this->validator    = null;
        $this->revisionMock = null;
    }

    /**
     * @expectedException Symfony\Component\Validator\Exception\MissingOptionsException
     */
    public function testRevisionIsRequired()
    {
        $this->validator->validate(array(), new RevisionConstraint());
    }

    /**
     * @expectedException Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public function testInvalidConstraintOption()
    {
        $this->validator->validate(array(), new RevisionConstraint(array("revision" => $this->revisionMock, "validateFields" => false, "validateValues" => false, "validateRules" => false, "validateSources" => false)));
    }

    public function dataInvalidValues()
    {
        return array(
            array(null),
            array("test"),
            array(1)
        );
    }

    /**
     * @dataProvider dataInvalidValues
     * @expectedException Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testInvalidValues($values)
    {
        $this->validator->validate($values, new RevisionConstraint(array("revision" => $this->revisionMock)));
    }

    public function dataValidateFields()
    {
        return array(
            array([42 => 'champ text', 200 => '#NA'], true),
            array([200 => '#NA', 42 => 'champ text'], true),
            array([42 => 'champ text'], false),
            array([200 => '#NA'], false),
            array([200 => '#NA', 52 => 'champ date'], false),

        );
    }

    /**
     * @dataProvider dataValidateFields
     */
    public function testValidateField($values, $isValid)
    {
        $this->revisionMock->expects($this->once())
            ->method('getContract')
            ->will($this->returnValue($this->contractMock));

        $this->contractMock->expects($this->once())
            ->method('getProductLine')
            ->will($this->returnValue($this->productLineMock));

        $this->productLineMock->expects($this->once())
            ->method('getFieldIds')
            ->will($this->returnValue(array(42, 200)));

        if ($isValid) {
            $this->context->expects($this->never())
                ->method('addViolation');
        } else {
            $this->context->expects($this->once())
                ->method('addViolation');
        }

        $this->validator->validate($values, new RevisionConstraint(
                array(
                    "revision"               => $this->revisionMock,
                    "validateValues"         => false,
                    "validateSources"        => false,
                    "validateRules" => false
                )
            )
        );

    }

    public function dataValidateValues()
    {
        return array(
            // text type
            array(Field::TYPE_TEXT, [42 => 'champ text'], true),
            array(Field::TYPE_TEXT, [42 => '#NA'], true),
            array(Field::TYPE_TEXT, [42 => ''], true),
            array(Field::TYPE_TEXT, [42 => 2], true),
            array(Field::TYPE_TEXT, [42 => 99999999998.999999999], true),

            // numeric type
            array(Field::TYPE_NUMERIC, [42 => 12], true),
            array(Field::TYPE_NUMERIC, [42 => 99999999998.999999999], true),
            array(Field::TYPE_NUMERIC, [42 => '#NA'], true),
            array(Field::TYPE_NUMERIC, [42 => ''], true),
            array(Field::TYPE_NUMERIC, [42 => 'texte'], false),

            // list type
            array(Field::TYPE_LIST, [42 => 3], true),
            array(Field::TYPE_LIST, [42 => 5], true),
            array(Field::TYPE_LIST, [42 => ''], true),
            array(Field::TYPE_LIST, [42 => '#NA'], true),
            array(Field::TYPE_LIST, [42 => '3'], true),
            array(Field::TYPE_LIST, [42 => '5'], true),
            array(Field::TYPE_LIST, [42 => 3.5], false),

            // date type
            array(Field::TYPE_DATE, [42 => '17/04/85'], false),
            array(Field::TYPE_DATE, [42 => '17/04/1985'], true),
            array(Field::TYPE_DATE, [42 => 5], false),

            //null value
            array(Field::TYPE_TEXT, [42 => null], true),
            array(Field::TYPE_DATE, [42 => null], true),
            array(Field::TYPE_LIST, [42 => null], true),
            array(Field::TYPE_NUMERIC, [42 => null], true),
        );
    }

    /**
     * @dataProvider dataValidateValues
     */
    public function testValidateValues($fieldTypeId, $values, $isValid)
    {
        $objectId = 42;

        $this->revisionMock->expects($this->once())
            ->method('getContract')
            ->will($this->returnValue($this->contractMock));

        $this->contractMock->expects($this->once())
            ->method('getProductLine')
            ->will($this->returnValue($this->productLineMock));

        $this->productLineMock->expects($this->once())
            ->method('getFieldIds')
            ->will($this->returnValue(array(42)));

        // Mock method getField for revisionSdk with $fieldMock
        $this->revisionMock->expects($this->any())
            ->method('getField')
            ->will($this->returnValue($this->fieldMock));

        // Mock method getTypeId for $fieldMock with $fieldTypeId
        $this->fieldMock->expects($this->any())
            ->method('getTypeId')
            ->will($this->returnValue($fieldTypeId));

        if (Field::TYPE_LIST == $fieldTypeId) {
            // Mock method getListId for $fieldMock with $objectId
            $this->fieldMock->expects($this->any())
                ->method('getListId')
                ->will($this->returnValue($objectId));

            $this->fieldMock->expects($this->any())
                ->method('getChoices')
                ->will($this->returnValue(array(3 => 'option 3', 5 => 'option 5')));
        }

        if ($isValid) {
            $this->context->expects($this->never())
                ->method('addViolation');
        } else {
            $this->context->expects($this->atLeastOnce())
                ->method('addViolation');
        }

        $this->validator->validate($values, new RevisionConstraint(
                array(
                    "revision"               => $this->revisionMock,
                    "validateSources"        => false,
                    "validateRules" => false
                )
            )
        );
    }

    public function dataValidateSources()
    {
        return array(
            array(Field::TYPE_NUMERIC, [42 => 12], true),
            array(Field::TYPE_NUMERIC, [42 => 12], false),
        );
    }

    /**
     * @dataProvider dataValidateSources
     */
    public function testValidateSources($fieldTypeId, $values, $hasSources)
    {
        $objectId = 42;

        $this->revisionMock->expects($this->once())
            ->method('getContract')
            ->will($this->returnValue($this->contractMock));

        $this->contractMock->expects($this->once())
            ->method('getProductLine')
            ->will($this->returnValue($this->productLineMock));

        $this->productLineMock->expects($this->once())
            ->method('getFieldIds')
            ->will($this->returnValue(array($objectId)));

        // Mock method getField for revisionSdk with $fieldMock
        $this->revisionMock->expects($this->any())
            ->method('getField')
            ->will($this->returnValue($this->fieldMock));

        $fieldSource = new RevisionFieldSource($this->getSdk('field'));
        if ($hasSources) {
            $this->revisionMock->expects($this->any())
                ->method('getFieldSource')
                ->will($this->returnValue($fieldSource));
        } else {
            $this->revisionMock->expects($this->any())
                ->method('getFieldSource')
                ->will($this->returnValue(array()));
        }

        // Mock method getTypeId for $fieldMock with $fieldTypeId
        $this->fieldMock->expects($this->any())
            ->method('getTypeId')
            ->will($this->returnValue($fieldTypeId));
        $this->fieldMock->expects($this->any())
            ->method('isSourceable')
            ->will($this->returnValue(true));

        if ($hasSources) {
            $this->context->expects($this->never())
                ->method('addViolation');
        } else {
            $this->context->expects($this->atLeastOnce())
                ->method('addViolation');
        }

        $this->validator->validate($values, new RevisionConstraint(
                array(
                    "revision"               => $this->revisionMock,
                    "validateRules" => false
                )
            )
        );
    }

    public function dataValidateDataEntryRules()
    {
        return array(
            array(
                [42 => 3], ["not empty"], true, true,
                [42 => '#NA'], [], true, true,
                [42 => '#NA'], [], false, true,
                [42 => 3], ["not empty"], false, false,
                [42 => '#NA'], ["not empty"], true, false,
                [42 => ''], ["not empty"], false, false,
            )
        );
    }

    /**
     * @dataProvider dataValidateDataEntryRules
     */
    public function testValidateDataEntryRules($values, $rulesTargetingField, $visibility, $isValid)
    {
        $this->revisionMock->expects($this->once())
            ->method('getContract')
            ->will($this->returnValue($this->contractMock));

        $this->contractMock->expects($this->once())
            ->method('getProductLine')
            ->will($this->returnValue($this->productLineMock));

        $this->revisionMock->expects($this->any())
            ->method('getField')
            ->will($this->returnValue($this->fieldMock));

        $this->connectorMock->expects($this->any())
            ->method('getRulesFromTargetFieldId')
            ->will($this->returnValue($rulesTargetingField));

        $this->connectorMock->expects($this->any())
            ->method('evaluateRulesByTarget')
            ->will($this->returnValue($visibility));

        if ($isValid) {
            $this->context->expects($this->never())
                ->method('addViolation');
        } else {
            $this->context->expects($this->atLeastOnce())
                ->method('addViolation');
        }

        $this->validator->validate($values, new RevisionConstraint(
                array(
                    "revision"        => $this->revisionMock,
                    "validateFields"  => false,
                    "validateValues"  => false,
                    "validateSources" => false,
                )
            )
        );
    }

}
