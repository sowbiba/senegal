<?php

namespace Senegal\Api\SdkBundle\Tests\Request\ParamConverter;

use Symfony\Component\HttpFoundation\Request;
use Pfd\Sdk\Mediator\SdkMediator;
use Senegal\Api\SdkBundle\Request\ParamConverter\ModelParamConverter;

/**
 * Class ModelParamConverterTest
 */
class ModelParamConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SdkMediator
     */
    private $sdkMediator;

    /**
     * @var ModelParamConverter
     */
    private $converter;

    public function setUp()
    {
        $this->sdkMediator = $this->getMockBuilder('Pfd\Sdk\Mediator\SdkMediator')->disableOriginalConstructor()->getMock();
        $this->converter = new ModelParamConverter($this->sdkMediator);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Unable to guess how to get a sdk model instance from the request information.
     */
    public function testApplyWithNoIdAndData()
    {
        $request = new Request();
        $config = $this->createConfiguration(null, array());
        $this->converter->apply($request, $config);
    }

    public function testApplyWithNoIdAndDataOptional()
    {
        $request = new Request();
        $config = $this->createConfiguration(null, array(), 'arg', true);

        $ret = $this->converter->apply($request, $config);

        $this->assertTrue($ret);
        $this->assertNull($request->attributes->get('arg'));
    }

    /**
     * @dataProvider idsProvider
     */
    public function testApplyWithId($id)
    {
        $request = new Request();
        $request->attributes->set('id', $id);

        $config = $this->createConfiguration('stdClass', array('id' => 'id'), 'arg');

        $sdk = $this->getMock('Pfd\Sdk\SdkInterface');

        $this->sdkMediator->expects($this->once())
            ->method('getSdkByClass')
            //->with('stdClass')
            ->will($this->returnValue($sdk));

        $sdk->expects($this->once())
            ->method('getById')
            ->with($this->equalTo($id))
            ->will($this->returnValue($object =new \stdClass));

        $ret = $this->converter->apply($request, $config);

        $this->assertTrue($ret);
        $this->assertSame($object, $request->attributes->get('arg'));
    }

    public function idsProvider()
    {
        return array(
            array(1),
            array(0),
        );
    }

    public function testApplyGuessOptional()
    {
        $request = new Request();
        $request->attributes->set('arg', null);

        $config = $this->createConfiguration('stdClass', array(), 'arg', null);

        $sdk = $this->getMock('Pfd\Sdk\SdkInterface');

        $this->sdkMediator->expects($this->never())->method('getSdkByClass');
        $sdk->expects($this->never())->method('getById');

        $ret = $this->converter->apply($request, $config);

        $this->assertTrue($ret);
        $this->assertNull($request->attributes->get('arg'));
    }

    public function testSupports()
    {
        $configuration = $this->createConfiguration('fakeClass');
        $this->assertFalse($this->converter->supports($configuration), 'param converter should not support wrong class');

        $configuration = $this->createConfiguration('Propel\PropelBundle\Tests\TestCase');
        $this->assertFalse($this->converter->supports($configuration), 'param converter should not support wrong class');

        $configuration = $this->createConfiguration('Pfd\Sdk\Model\Chapter');
        $this->assertTrue($this->converter->supports($configuration), 'param converter should support sdk model class');
    }

    protected function createConfiguration($class = null, array $options = null, $name = 'arg', $isOptional = false)
    {
        $methods = array('getClass', 'getAliasName', 'getOptions', 'getName', 'allowArray');
        if (null !== $isOptional) {
            $methods[] = 'isOptional';
        }
        $config = $this
            ->getMockBuilder('Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter')
            ->setMethods($methods)
            ->disableOriginalConstructor()
            ->getMock();
        if ($options !== null) {
            $config->expects($this->once())
                ->method('getOptions')
                ->will($this->returnValue($options));
        }
        if ($class !== null) {
            $config->expects($this->any())
                ->method('getClass')
                ->will($this->returnValue($class));
        }
        $config->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));
        if (null !== $isOptional) {
            $config->expects($this->any())
                ->method('isOptional')
                ->will($this->returnValue($isOptional));
        }

        return $config;
    }
}
