<?php

namespace Api\SdkBundle\Tests\Security\User;

use Api\Sdk\Bridge\LegacyBridge;
use Api\Sdk\Model\User;
use Api\SdkBundle\Security\User\SdkUserProvider;
use Symfony\Component\Security\Core\User\UserInterface;

class SdkUserProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $sdk;

    /**
     * @var SdkUserProvider
     */
    protected $provider;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var LegacyBridge
     */
    protected $bridge;

    /**
     * Setup
     */
    public function setUp()
    {
        $this->sdk = $this->getMockBuilder('Api\Sdk\User\UserSdk')
            ->disableOriginalConstructor()
            ->getMock();

        $this->user = $this->getMockBuilder('Api\Sdk\Model\User')
            ->disableOriginalConstructor()
            ->getMock();

        $this->bridge = $this->getMockBuilder('Api\Sdk\Bridge\LegacyBridge')
            ->disableOriginalConstructor()
            ->getMock();

        $this->user = new User($this->sdk);
        $this->user->setId(42);
        $this->user->setUsername('test');
        $this->user->setPassword('toto');
        $this->user->setActive(true);

        $this->provider = new SdkUserProvider($this->sdk, $this->bridge);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     * @expectedExceptionMessage Username "titi" does not exist.
     */
    public function testLoadUserByUsernameUsernameNotFoundException()
    {
        $this->sdk->expects($this->once())
            ->method('getByUsername')
            ->with('titi')
            ->will($this->returnValue(null));

        $this->provider->loadUserByUsername('titi');
    }

    /**
     * test the loadUserByUsername method
     */
    public function testLoadUserByUsername()
    {
        $this->sdk->expects($this->once())
            ->method('getByUsername')
            ->with($this->user->getUsername())
            ->will($this->returnValue($this->user));

        $user = $this->provider->loadUserByUsername($this->user->getUsername());

        $this->assertEquals($user->getId(), $this->user->getId());
        $this->assertEquals($user->getUsername(), $this->user->getUsername());
        $this->assertEquals($user->getPassword(), $this->user->getPassword());
        $this->assertEquals($user->getRoles(), $this->user->getRoles());
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UnsupportedUserException
     * @expectedExceptionMessage Instances of "Api\SdkBundle\Tests\Security\User\SomeUser" are not supported.
     */
    public function testRefreshUserUnsupportedUserException()
    {
        $this->provider->refreshUser(new SomeUser());
    }

    /**
     * test the refreshUser method
     */
    public function testRefreshUser()
    {
        $user = new User($this->sdk);
        $user->setUsername($this->user->getUsername());

        $this->sdk->expects($this->once())
            ->method('getByUsername')
            ->with($this->user->getUsername())
            ->will($this->returnValue($this->user));

        $this->bridge->expects($this->once())
            ->method('permissiveTransaction');

        $refreshedUser = $this->provider->refreshUser($user);

        $this->assertEquals($refreshedUser->getId(), $this->user->getId());
        $this->assertEquals($refreshedUser->getUsername(), $this->user->getUsername());
        $this->assertEquals($refreshedUser->getPassword(), $this->user->getPassword());
    }

    /**
     * Test the supportsClass method
     */
    public function testSupportsClass()
    {
        $this->assertFalse($this->provider->supportsClass('\stdClass'));
        $this->assertTrue($this->provider->supportsClass('Api\Sdk\Model\User'));
    }
}

class SomeUser implements UserInterface
{
    public function getRoles() {}
    public function getPassword() {}
    public function getSalt() {}
    public function getUsername() {}
    public function eraseCredentials() {}
}
