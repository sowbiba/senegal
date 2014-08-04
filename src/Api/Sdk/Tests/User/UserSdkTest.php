<?php
namespace Api\Sdk\Tests\User;

use Api\Sdk\Tests\SdkTestCase;

class UserSdkTest extends SdkTestCase
{

    public function testGetUser()
    {
        $user = $this->getSdk("user")->getById(1);

        $this->assertInstanceOf('Api\Sdk\Model\User', $user);
        $this->assertEquals(1, $user->getId());
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testGetUserWithBadParameter()
    {
        $this->getSdk("user")->getById("p_carole");
    }

}
