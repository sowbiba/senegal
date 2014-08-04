<?php

namespace Api\SdkBundle\Tests\Listener;

use Api\Sdk\AbstractSdk;
use Api\Sdk\Event\Events;
use Api\Sdk\Model\BaseModel;
use Api\Sdk\Model\UploadableInterface;
use Api\Sdk\SdkInterface;
use Api\SdkBundle\Listener\UploadableListener;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class UploadableListenerTest
 */
class UploadableListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $sdk;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $file;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $slugifier;

    /**
     * Setup the sdk
     */
    protected function setUp()
    {
        //@todo Tests fail in CI server after php update in 5.4.30. Fix it
        $this->markTestSkipped('Fail in CI server after php update in 5.4.30. Fix it');

        $connector = $this->getMock('Api\Sdk\Connector\ConnectorInterface');
        $this->slugifier = $this->getMockBuilder('Api\SdkBundle\Tools\UploadedFileSlugifier')
            ->disableOriginalConstructor()
            ->getMock();

        $this->slugifier->expects($this->any())
            ->method('getRelativeFilePath')
            ->will($this->returnValue('filepath'));

        $this->slugifier->expects($this->any())
            ->method('getWebDir')
            ->will($this->returnValue('/tmp/'));

        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(Events::PRE_CREATE, array(new UploadableListener($this->slugifier), 'preCreate'));
        $dispatcher->addListener(Events::PRE_UPDATE, array(new UploadableListener($this->slugifier), 'preUpdate'));
        $dispatcher->addListener(Events::PRE_DELETE, array(new UploadableListener($this->slugifier), 'preDelete'));

        $this->sdk = new UploadableSdkMock($connector, $dispatcher);
        $this->file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
            ->disableOriginalConstructor()
            ->getMock();

        $this->file->expects($this->any())
            ->method('getClientSize')
            ->will($this->returnValue(42));
    }

    /**
     * Test the preCreate listener
     */
    public function testCreate()
    {
        $model = new UploadableModelMock($this->file);
        $this->sdk->create($model);

        $this->assertEquals(42, $model->size);
        $this->assertEquals('filepath', $model->filepath);
    }

    /**
     * Test the preUpdate listener
     */
    public function testUpdate()
    {
        $model = new UploadableModelMock($this->file);
        $model->size = 30;
        $model->filepath = 'another';

        $this->sdk->update($model);

        $this->assertEquals(42, $model->size);
        $this->assertEquals('filepath', $model->filepath);
    }

    /**
     * Test the preDelete listener
     */
    public function testDelete()
    {
        $model = new UploadableModelMock($this->file);
        $model->filepath = 'sdk-upload-delete-test';

        file_put_contents('/tmp/sdk-upload-delete-test', 'Test file');

        $this->assertTrue(file_exists('/tmp/sdk-upload-delete-test'));
        $this->sdk->delete($model);
        $this->assertFalse(file_exists('/tmp/sdk-upload-delete-test'));
    }
}

class UploadableModelMock extends BaseModel implements UploadableInterface
{
    /** @var UploadedFile */
    public $file;

    /** @var string */
    public $filepath;

    /** @var integer */
    public $size;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getFilePath()
    {
        return $this->filepath;
    }

    public function setSize($size)
    {
        $this->size = $size;
    }

    public function setFilePath($filePath)
    {
        $this->filepath = $filePath;
    }
}

class UploadableSdkMock extends AbstractSdk implements SdkInterface
{
    public function doCreate($data) {}
    public function doUpdate($data) {}
    public function doDelete(BaseModel $object) {}
    public function supports($classname) {}
}
