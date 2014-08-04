<?php
namespace Api\Sdk\Tests\Model;

use Api\Sdk\Model\User;
use Api\Sdk\Tests\SdkTestCase;

class UserTest extends SdkTestCase
{
    public function testCreateFromArray()
    {
        $userData = [
            'id'        => 1,
            'firstname' => 'John',
            'lastname'  => 'Doe',
            'username'  => 'john.doe',
            'salt'      => 'rthdrth4386rth4drthdrth4drthdrth45',
            'password'  => 'doepass',
            'email'     => 'doepass@profideo.com',
            'roles'     => ['ROLE_ADMIN', 'ROLE_USER'],
            'type'      => User::TYPE_INTERNAL,
        ];

        // Create a fake contract for testing
        $user = new User($this->getSdk("user"), $userData);

        // Assert
        $this->assertEquals($user->getId(), $userData['id']);
        $this->assertEquals((string) $user, $userData['firstname'] . ' ' . $userData['lastname']);
        $this->assertEquals($user->getFirstname(), $userData['firstname']);
        $this->assertEquals($user->getLastname(), $userData['lastname']);
        $this->assertEquals($user->getUsername(), $userData['username']);
        $this->assertEquals($user->getSalt(), $userData['salt']);
        $this->assertEquals($user->getPassword(), $userData['password']);
        $this->assertEquals($user->getEmail(), $userData['email']);
        $this->assertEquals($user->getRoles(), ['ROLE_ADMIN', 'ROLE_USER']);
        $this->assertEquals($user->getType(), User::TYPE_INTERNAL);
        $this->assertFalse($user->isClient());
        $this->assertTrue($user->isInternal());
    }
}
