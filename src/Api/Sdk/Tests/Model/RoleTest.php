<?php
namespace Api\Sdk\Tests\Model;

use Api\Sdk\Model\Role;
use Api\Sdk\Tests\SdkTestCase;

/**
 * Class RoleTest
 */
class RoleTest extends SdkTestCase
{
    /**
     * Test createFromArray method
     */
    public function testCreateFromArray()
    {
        $roleData = [
            'id'              => 1,
            'description'     => 'Test description from phpunit',
            'name'            => 'test_name_phpunit',
        ];

        $role = new Role($this->getSdk('role'), $roleData);

        $this->assertEquals($role->getId(), $roleData['id']);
        $this->assertEquals($role->getDescription(), $roleData['description']);
    }

    /**
     * Returns class setter methods
     *
     * @return array
     */
    public function getSetter()
    {
        return [
            ['setId'],
            ['setDescription'],
            ['setName'],
        ];
    }

    /**
     * Tests class setter methods return instance
     *
     * @dataProvider getSetter
     *
     * @param string $setter Setter method name
     */
    public function testSetterReturnInstance($setter)
    {
        $role = new Role($this->getSdk('role'));

        $this->assertSame($role, $role->$setter('test'));
    }

    /*
     * Test __toString method
     */
    public function testToString()
    {
        $role = new Role($this->getSdk('role'));
        $role->setDescription('Test description from phpunit');

        $this->assertSame($role->getDescription(), (string) $role);
    }
}
