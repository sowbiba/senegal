<?php

namespace Api\Sdk\Tests\Role;

use Api\Sdk\Tests\SdkTestCase;
use Api\Sdk\Role\Query\RoleQuery;

/**
 * Class RoleSdkTest
 */
class RoleSdkTest extends SdkTestCase
{
    /**
     * Roles data provider
     *
     * @return array
     */
    public function rolesProvider()
    {
        return [
            [[], [1, 2]],
            [['name' => 'role_1'], [1]],
            [['name' => 'role_2'], [2]],
        ];
    }

    /**
     * Test RoleSdk::getCollection
     *
     * @dataProvider rolesProvider
     */
    public function testGetCollection($filters, $rolesExpected)
    {
        $roles = $this->getSdk("role")->getCollection(new RoleQuery($filters));

        $this->assertCount(count($rolesExpected), $roles);

        foreach ($roles as $role) {
            $this->assertInstanceOf('Api\Sdk\Model\Role', $role);
            $this->assertContains($role->getId(), $rolesExpected);
        }
    }

    /**
     * Test RoleSdk::getAll
     *
     */
    public function testGetAll()
    {
        $roles = $this->getSdk("role")->getAll();

        $this->assertCount(2, $roles);
    }
}
