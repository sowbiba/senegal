<?php

namespace Senegal\BackBundle\Tests\Entity;

use Senegal\BackBundle\Entity\Role;
use Senegal\BackBundle\Tests\BaseUnitTestCase;

class RoleTest extends BaseUnitTestCase
{
    public function testConstants()
    {
        $role = new Role();
        $this->assertEquals('agence', $role::FRONT_ROLE);
        $this->assertEquals(4, $role::FRONT_ROLE_ID);
        $this->assertEquals('admin_full', $role::ADMIN_ROLE);
        $this->assertEquals(2, $role::ADMIN_ROLE_ID);
        $this->assertEquals('back', $role::BACK_ROLE);
        $this->assertEquals(1, $role::BACK_ROLE_ID);
    }

    public function testId()
    {
        $role = new Role();
        $this->assertNull($role->getId());

        $role->setId(1);
        $this->assertEquals(1, $role->getId());
    }

    public function testName()
    {
        $role = new Role();
        $this->assertNull($role->getName());

        $role->setName('My role name');
        $this->assertEquals('My role name', $role->getName());
        $this->assertEquals('My role name', $role->__toString());
    }

    public function testCreatedAt()
    {
        $role = new Role();
        $this->assertNull($role->getCreatedAt());
    }

    public function testUpdatedAt()
    {
        $role = new Role();
        $this->assertNull($role->getUpdatedAt());
    }

    public function testToString()
    {
        $role = new Role();
        $this->assertEquals('', $role->__toString());

        $role->setName("Role 1");
        $this->assertEquals('Role 1', $role->__toString());
    }
}
