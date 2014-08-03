<?php

namespace Senegal\Api\SdkBundle\Tests\Listener;

use Pfd\Sdk\AbstractSdk;
use Pfd\Sdk\Event\Events;
use Pfd\Sdk\Model\BlameableInterface;
use Pfd\Sdk\SdkInterface;
use Senegal\Api\SdkBundle\Listener\BlameableListener;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class BlameableListenerTest
 */
class BlameableListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $sdk;

    /**
     * Setup the sdk
     */
    protected function setUp()
    {
        $connector = $this->getMock('Pfd\Sdk\Connector\ConnectorInterface');
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');

        $user->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue('test'));

        $token->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($user));

        $securityContext->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($token));

        $container->expects($this->once())
            ->method('get')
            ->with('security.context')
            ->will($this->returnValue($securityContext));

        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(Events::PRE_CREATE, array(new BlameableListener($container), 'preCreate'));
        $dispatcher->addListener(Events::PRE_UPDATE, array(new BlameableListener($container), 'preUpdate'));

        $this->sdk = new BlameableSdkMock($connector, $dispatcher);
    }

    /**
     * Test the preCreate listener
     */
    public function testCreate()
    {
        $model = new BlameableModelMock();
        $this->sdk->create($model);

        $this->assertNotEmpty($model->createdBy);
        $this->assertNotEmpty($model->updatedBy);

        $this->assertTrue($model->createdBy instanceof UserInterface);
        $this->assertTrue($model->updatedBy instanceof UserInterface);

        $this->assertEquals('test', $model->createdBy->getUsername());
        $this->assertEquals('test', $model->updatedBy->getUsername());
    }

    /**
     * Test the preUpdate listener
     */
    public function testUpdate()
    {
        $model = new BlameableModelMock();
        $this->sdk->update($model);

        $this->assertEmpty($model->createdBy);
        $this->assertNotEmpty($model->updatedBy);
        $this->assertTrue($model->updatedBy instanceof UserInterface);
        $this->assertEquals('test', $model->updatedBy->getUsername());
    }
}

class BlameableModelMock implements BlameableInterface
{
    /** @var UserInterface */
    public $updatedBy;

    /** @var UserInterface */
    public $createdBy;

    public function setCreatedBy(UserInterface $createdBy)
    {
        $this->createdBy = $createdBy;
    }

    public function setUpdatedBy(UserInterface $updatedBy)
    {
        $this->updatedBy = $updatedBy;
    }
}

class BlameableSdkMock extends AbstractSdk implements SdkInterface
{
    public function doCreate($data) {}
    public function doUpdate($data) {}
    public function supports($classname) {}
}
