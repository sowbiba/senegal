<?php

namespace Senegal\ApiBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Senegal\ApiBundle\Entity\Group;
use Senegal\ApiBundle\Entity\Role;
use Senegal\ApiBundle\Entity\User;
use Senegal\ApiBundle\Entity\UserProfile;
use Senegal\ApiBundle\Tests\BaseUnitTestCase;

class UserTest extends BaseUnitTestCase
{

    /**
     *
     * username
     * algorithm
     * active
    password
    lastname
    firstname
    address
    phone
    mail
    roleId
     */



    private $emptyToArray = [
        'login' => null,
        'isActive' => false,
        'password' => null,
        'profile' => [
            'name' => null,
            'firstName' => null,
            'email' => null,
            'phone' => null,
            'address' => null,
            'address2' => null,
            'zipCode' => null,
            'city' => null,
            'group' => null,
            'testEndDate' => null,
            'isLockedOut' => false,
            'receiveMail' => false,
        ],
    ];

    private $fullToArray = [
        'username' => 'username',
        'isActive' => true,
        'profile' => [
            'name' => 'Name',
            'firstName' => 'FirstName',
            'email' => 'test@profideo.com',
            'phone' => '0102030405',
            'address' => '1, rue de test',
            'address2' => 'Complement',
            'zipCode' => '75000',
            'city' => 'PARIS',
            'isLockedOut' => true,
            'receiveMail' => true,
        ],
    ];

    public function testId()
    {
        $user = new User();
        $this->assertNull($user->getId());

        $user->setId(1);
        $this->assertEquals(1, $user->getId());
    }

    public function testActive()
    {
        $user = new User();

        $this->assertFalse($user->getIsActive());
        $this->assertFalse($user->isActive());

        $user->setIsActive(false);
        $this->assertFalse($user->getIsActive());
        $this->assertFalse($user->isActive());

        $user->setIsActive(true);
        $this->assertTrue($user->getIsActive());
        $this->assertTrue($user->isActive());
    }

    public function testAlgorithm()
    {
        $user = new User();

        $this->assertEquals('sha1', $user->getAlgorithm());
        $user->setPassword('password');
        $this->assertEquals(hash('sha1', $user->getSalt().strtolower('password')), $user->getPassword());

        $user->setAlgorithm('md5');
        $this->assertEquals('md5', $user->getAlgorithm());
        $user->setPassword('password');
        $this->assertEquals(hash('md5', $user->getSalt().strtolower('password')), $user->getPassword());
    }

    public function testPassword()
    {
        $user = new User();

        $this->assertNull($user->getPassword());

        $user->setPassword('password');
        $this->assertEquals(hash('sha1', $user->getSalt().strtolower('password')), $user->getPassword());

        $user->setPassword(123);
        $this->assertEquals(hash('sha1', $user->getSalt().strtolower(123)), $user->getPassword());
    }

    public function testLogin()
    {
        $user = new User();

        $this->assertNull($user->getUsername());

        $user->setUsername('username');
        $this->assertEquals('username', $user->getUsername());

        $user->setUsername(123);
        $this->assertEquals(123, $user->getUsername());
    }

    public function xtestUsernameAssertNotBlankConstraint()
    {
        $user = new User();

        $violationList = $this->getConstraintsValidator()->validateProperty($user, 'username');
        $this->assertEquals(1, $violationList->count());
        $this->assertNull($violationList->get(0)->getInvalidValue());
        $this->assertEquals('user.fields.empty.username', $violationList->get(0)->getMessage());

        $user->setUsername('');
        $this->assertEmpty($user->getUsername());

        $violationList = $this->getConstraintsValidator()->validateProperty($user, 'username');
        $this->assertEquals(1, $violationList->count());
        $this->assertEmpty($violationList->get(0)->getInvalidValue());
        $this->assertEquals('user.fields.empty.username', $violationList->get(0)->getMessage());
    }

    public function testSalt()
    {
        $user = new User();
        $this->assertNotNull($user->getSalt());
    }

    public function testTimestamp()
    {
        $user = new User();

        $this->assertNull($user->getCreatedAt());
        $this->assertNull($user->getUpdatedAt());
        $this->assertNull($user->getCreatedBy());
        $this->assertNull($user->getUpdatedBy());

        $now = new \DateTime();
        $user->setUpdatedAt($now);
        $this->assertSame($now, $user->getUpdatedAt());

        $user2 = new User();
        $user2->setId(2);
        $user->setCreatedBy($user2);
        $this->assertEquals(2, $user->getCreatedBy()->getId());
        $this->assertNull($user->getUpdatedBy());

        $user->setUpdatedBy($user2);
        $this->assertEquals(2, $user->getCreatedBy()->getId());
        $this->assertEquals(2, $user->getUpdatedBy()->getId());
    }

    public function testEmail()
    {
        $user = new User();

        $this->assertNull($user->getEmail());

        $user->setEmail('wrongEmail');
        $this->assertEquals('wrongEmail', $user->getEmail());

        $user->setEmail(123);
        $this->assertEquals(123, $user->getEmail());
    }

    public function testName()
    {
        $user = new User();

        $this->assertNull($user->getName());

        $user->setLastname('name');
        $this->assertEquals('name', $user->getName());

        $user->setLastname(123);
        $this->assertEquals(123, $user->getName());
    }

    public function testFirstName()
    {
        $user = new User();

        $this->assertNull($user->getFirstName());

        $user->setFirstName('Firstname');
        $this->assertEquals('Firstname', $user->getFirstName());

        $user->setFirstName(123);
        $this->assertEquals(123, $user->getFirstName());
    }

    public function testPhone()
    {
        $user = new User();

        $this->assertNull($user->getPhone());

        $user->setPhone('0102030405');
        $this->assertEquals('0102030405', $user->getPhone());

        $user->setPhone(123);
        $this->assertEquals(123, $user->getPhone());
    }

    public function testAddress()
    {
        $user = new User();

        $this->assertNull($user->getAddress());

        $user->setAddress('11, rue de test');
        $this->assertEquals('11, rue de test', $user->getAddress());

        $user->setAddress(123);
        $this->assertEquals(123, $user->getAddress());
    }

    public function testRole()
    {
        $user = new User();

        $this->assertNull($user->getRole());
        $this->assertCount(0, $user->getRoles()->toArray());

        $role1 = new Role();
        $role1->setId(1);
        $role1->setName("Role 1");

        $user->setRole($role1);
        $this->assertCount(1, $user->getRoles());
        $this->assertInstanceOf("\\Senegal\\ApiBundle\\Entity\\Role", $user->getRole());

        $user->setRole($role1);
        $this->assertCount(1, $user->getRoles());
        $this->assertEquals(1, $user->getRoleId());
        $this->assertEquals("Role 1", $user->getRoleName());
    }

    public function testRoleRequired()
    {
        $user = new User();

        $role = new Role();
        $role->setId(Role::FRONT_ROLE_ID);
        $role->setName(Role::FRONT_ROLE);

        $user->setPassword('password');
        $user->setEmail('email');
        $user->setUsername('username');

        $violationList = $this->getConstraintsValidator()->validate($user);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals('user.fields.empty.role', $violationList->get(0)->getMessage());

        /* with role set */
        $user->setRole($role);
        $violationList = $this->getConstraintsValidator()->validate($user);
        $this->assertEquals(0, $violationList->count());
    }

    public function testPasswordRequired()
    {
        $user = new User();

        $role = new Role();
        $role->setId(Role::FRONT_ROLE_ID);
        $role->setName(Role::FRONT_ROLE);

        $user->setUsername('username');
        $user->setRole($role);
        $user->setEmail('email');

        $violationList = $this->getConstraintsValidator()->validate($user);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals('user.fields.empty.password', $violationList->get(0)->getMessage());

        /* with password set */
        $user->setPassword('password');
        $violationList = $this->getConstraintsValidator()->validate($user);
        $this->assertEquals(0, $violationList->count());
    }

    public function testUsernameRequired()
    {
        $user = new User();

        $role = new Role();
        $role->setId(Role::FRONT_ROLE_ID);
        $role->setName(Role::FRONT_ROLE);

        $user->setPassword('password');
        $user->setEmail('email');
        $user->setRole($role);

        $violationList = $this->getConstraintsValidator()->validate($user);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals('user.fields.empty.username', $violationList->get(0)->getMessage());

        /* with username set */
        $user->setUsername('username');
        $violationList = $this->getConstraintsValidator()->validate($user);
        $this->assertEquals(0, $violationList->count());
    }

    public function testEmailRequired()
    {
        $user = new User();

        $role = new Role();
        $role->setId(Role::FRONT_ROLE_ID);
        $role->setName(Role::FRONT_ROLE);

        $user->setPassword('password');
        $user->setUsername('username');
        $user->setRole($role);

        $violationList = $this->getConstraintsValidator()->validate($user);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals('user.fields.empty.email', $violationList->get(0)->getMessage());

        /* with email set */
        $user->setEmail('email');
        $violationList = $this->getConstraintsValidator()->validate($user);
        $this->assertEquals(0, $violationList->count());
    }

    public function xtestToArray()
    {
        $user = new User();
        $userProfile = new UserProfile();
        $user->setUserProfile($userProfile);

        // test with empty data
        $resultArray = $user->getConcurrencyArray();
        $resultRole = $resultArray['role'];
        unset($resultArray['role']);

        $this->assertEquals($this->emptyToArray, $resultArray);
        $this->assertCount(0, $resultRole);

        // test with all data set
        $user->setUsername("username");
        $user->setIsActive(true);
        $user->setPassword("password");
        $userProfile->setName("Name");
        $userProfile->setFirstName("FirstName");
        $userProfile->setEmail("test@profideo.com");
        $userProfile->setPhone("0102030405");
        $userProfile->setAddress("1, rue de test");
        $userProfile->setAddress2("Complement");
        $userProfile->setZipCode("75000");
        $userProfile->setCity("PARIS");
        $userProfile->setTestEndDate('31/12/2020');
        $userProfile->setIsLockedOut(true);
        $userProfile->setReceiveMail(true);

        $role = new Role();
        $role->setId(1);
        $role->setName('Role1');
        $user->setRole($role);

        $group = new Group();
        $userProfile->setGroup($group);

        $resultArray = $user->getConcurrencyArray();
        $resultGroup = $resultArray['profile']['group'];
        $resultTestEndDate = $resultArray['profile']['testEndDate']->format('d/m/Y');
        $resultRole = $resultArray['role'];
        $resultPassword = $resultArray['password'];

        unset($resultArray['role']);
        unset($resultArray['password']);
        unset($resultArray['profile']['group']);
        unset($resultArray['profile']['testEndDate']);

        $this->assertEquals(hash('sha1', $user->getSalt().strtolower('password')), $resultPassword);
        $this->assertEquals($this->fullToArray, $resultArray);
        $this->assertEquals($group, $resultGroup);
        $this->assertEquals(new ArrayCollection([[1, 'Role1']]), $resultRole);
        $this->assertEquals('31/12/2020', $resultTestEndDate);
    }
}
