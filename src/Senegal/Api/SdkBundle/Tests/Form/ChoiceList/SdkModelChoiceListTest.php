<?php

namespace Senegal\Api\SdkBundle\Tests\Form\ChoiceList;
use Senegal\Api\SdkBundle\Form\ChoiceList\SdkModelChoiceList;

/**
 * Class SdkModelChoiceListTest
 */
class SdkModelChoiceListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $choices;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $manager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $sdk;

    /**
     *
     */
    protected function setUp()
    {
        $model1 = $this->getMockBuilder('stdClass')
            ->setMethods(array('__toString', 'getId'))
            ->disableOriginalConstructor()
            ->getMock();

        $model2 = $this->getMockBuilder('stdClass')
            ->setMethods(array('__toString', 'getId'))
            ->disableOriginalConstructor()
            ->getMock();

        $model1->expects($this->any())
            ->method('__toString')
            ->will($this->returnValue('First'));

        $model1->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(10));

        $model2->expects($this->any())
            ->method('__toString')
            ->will($this->returnValue('Second'));

        $model2->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(20));

        $this->choices = array($model2, $model1);

        $this->manager = $this->getMockBuilder('Pfd\Sdk\Mediator\SdkMediator')
            ->setMethods(array('getSdkByClass'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->sdk = $this->getMockBuilder('Pfd\Sdk\SdkInterface')
            ->setMethods(array('getAll'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
    }

    /**
     * Test that the sdk is not called when we directly use choices
     */
    public function testWithChoices()
    {
        $this->manager->expects($this->never())->method('getSdkByClass');

        $choiceList = new SdkModelChoiceList(
            $this->manager,
            "stdClass",
            null,
            $this->choices
        );

        $choiceList->getValues();
    }

    /**
     * Test that we get the good values
     */
    public function testValues()
    {
        $this->manager->expects($this->once())
            ->method('getSdkByClass')
            ->with('stdClass')
            ->will($this->returnValue($this->sdk));

        $this->sdk->expects($this->once())
            ->method('getAll')
            ->will($this->returnValue($this->choices));

        $choiceList = new SdkModelChoiceList($this->manager, 'stdClass');

        $this->assertSame(array(20 => '20', 10 => '10'), $choiceList->getValues());
    }

    /**
     * Test the order_by parameter
     */
    public function testOrderBy()
    {
        $this->manager->expects($this->once())
            ->method('getSdkByClass')
            ->with('stdClass')
            ->will($this->returnValue($this->sdk));

        $this->sdk->expects($this->once())
            ->method('getAll')
            ->will($this->returnValue($this->choices));

        $choiceList = new SdkModelChoiceList($this->manager, 'stdClass', null, null, null, 'id');

        $this->assertSame(array(10 => '10', 20 => '20'), $choiceList->getValues());
    }
}
