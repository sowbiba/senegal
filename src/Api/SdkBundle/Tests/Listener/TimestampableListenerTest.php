<?php

namespace Api\SdkBundle\Tests\Listener;

use Api\Sdk\AbstractSdk;
use Api\Sdk\Event\Events;
use Api\Sdk\Model\TimestampableInterface;
use Api\Sdk\SdkInterface;
use Api\SdkBundle\Listener\TimestampableListener;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class TimestampableListenerTest
 */
class TimestampableListenerTest extends \PHPUnit_Framework_TestCase
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
        $connector = $this->getMock('Api\Sdk\Connector\ConnectorInterface');

        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(Events::PRE_CREATE, array(new TimestampableListener(), 'preCreate'));
        $dispatcher->addListener(Events::PRE_UPDATE, array(new TimestampableListener(), 'preUpdate'));

        $this->sdk = new TimestampableSdkMock($connector, $dispatcher);
    }

    /**
     * Test the preCreate listener
     */
    public function testCreate()
    {
        $model = new TimestampableModelMock();
        $this->sdk->create($model);

        $this->assertNotEmpty($model->createdAt);
        $this->assertNotEmpty($model->updatedAt);

        $this->assertTrue($model->createdAt instanceof \DateTime);
        $this->assertTrue($model->updatedAt instanceof \DateTime);

        $this->assertEmpty((int) $model->createdAt->diff(new \DateTime('now', new \DateTimeZone('Europe/Paris')))->format('%s'));
        $this->assertEmpty((int) $model->updatedAt->diff(new \DateTime('now', new \DateTimeZone('Europe/Paris')))->format('%s'));
    }

    /**
     * Test the preUpdate listener
     */
    public function testUpdate()
    {
        $model = new TimestampableModelMock();
        $this->sdk->update($model);

        $this->assertEmpty($model->createdAt);
        $this->assertNotEmpty($model->updatedAt);
        $this->assertTrue($model->updatedAt instanceof \DateTime);
        $this->assertEmpty((int) $model->updatedAt->diff(new \DateTime('now', new \DateTimeZone('Europe/Paris')))->format('%s'));
    }
}

class TimestampableModelMock implements TimestampableInterface
{
    /** @var \Datetime */
    public $updatedAt;

    /** @var \Datetime */
    public $createdAt;

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
}

class TimestampableSdkMock extends AbstractSdk implements SdkInterface
{
    public function doCreate($data) {}
    public function doUpdate($data) {}
    public function supports($classname) {}
}
