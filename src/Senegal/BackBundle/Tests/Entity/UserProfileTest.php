<?php

namespace Senegal\BackBundle\Tests\Entity;

use Senegal\BackBundle\Entity\Group;
use Senegal\BackBundle\Entity\UserProfile;
use Senegal\BackBundle\Tests\BaseUnitTestCase;

class UserProfileTest extends BaseUnitTestCase
{
    private $emptyToArray = [
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
    ];

    private $fullToArray = [
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
    ];

    public function testId()
    {
        $userProfile = new UserProfile();
        $this->assertNull($userProfile->getId());

        $userProfile->setId(1);
        $this->assertEquals(1, $userProfile->getId());
    }

    public function testName()
    {
        $userProfile = new UserProfile();
        $this->assertNull($userProfile->getName());

        $userProfile->setName('My user lastname');
        $this->assertEquals('My user lastname', $userProfile->getName());
    }

    public function testFirstName()
    {
        $userProfile = new UserProfile();
        $this->assertNull($userProfile->getFirstName());

        $userProfile->setFirstName('My user firstname');
        $this->assertEquals('My user firstname', $userProfile->getFirstName());
    }

    public function testFullName()
    {
        $userProfile = new UserProfile();
        $this->assertEquals('', $userProfile->getFullName());

        $userProfile->setName('lastname');
        $this->assertEquals('lastname', $userProfile->getFullName());

        $userProfile->setName('lastname');
        $userProfile->setFirstName('firstname');
        $this->assertEquals('firstname lastname', $userProfile->getFullName());

        $userProfile->setName(null);
        $this->assertEquals('firstname', $userProfile->getFullName());
    }

    public function testTechnicalName()
    {
        $userProfile = new UserProfile();
        $this->assertEquals('-', $userProfile->getTechnicalName());

        $userProfile->setName('lastname');
        $this->assertEquals('lastname', $userProfile->getTechnicalName());

        $userProfile->setName('lastname');
        $userProfile->setFirstName('firstname');
        $this->assertEquals('firstname lastname', $userProfile->getTechnicalName());

        $userProfile->setName(null);
        $this->assertEquals('firstname', $userProfile->getTechnicalName());

        $userProfile->setName(null);
        $userProfile->setFirstName(null);
        $userProfile->setIsSso(true);
        $this->assertEquals('SSO', $userProfile->getTechnicalName());
    }

    public function testEmail()
    {
        $userProfile = new UserProfile();
        $this->assertNull($userProfile->getEmail());

        $userProfile->setEmail('user@profideo.com');
        $this->assertEquals('user@profideo.com', $userProfile->getEmail());
    }

    public function testEmailAssertEmailConstraint()
    {
        $userProfile = new UserProfile();
        $userProfile->setEmail('john.doe');

        $violationList = $this->getConstraintsValidator()->validateProperty($userProfile, 'email');
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals('john.doe', $violationList->get(0)->getInvalidValue());
        $this->assertEquals('user.fields.not_valid.email', $violationList->get(0)->getMessage());
    }

    public function testPhone()
    {
        $userProfile = new UserProfile();
        $this->assertNull($userProfile->getPhone());

        $userProfile->setPhone('0102030405');
        $this->assertEquals('0102030405', $userProfile->getPhone());
    }

    public function testAddress()
    {
        $userProfile = new UserProfile();
        $this->assertNull($userProfile->getAddress());

        $userProfile->setAddress('11, rue de test');
        $this->assertEquals('11, rue de test', $userProfile->getAddress());
    }

    public function testAddress2()
    {
        $userProfile = new UserProfile();
        $this->assertNull($userProfile->getAddress2());

        $userProfile->setAddress2("Complément d'adresse");
        $this->assertEquals("Complément d'adresse", $userProfile->getAddress2());
    }

    public function testZipCode()
    {
        $userProfile = new UserProfile();
        $this->assertNull($userProfile->getZipCode());

        $userProfile->setZipCode('75000');
        $this->assertEquals('75000', $userProfile->getZipCode());

        $userProfile->setZipCode(75000);
        $this->assertEquals('75000', $userProfile->getZipCode());

        $userProfile->setZipCode('ABC');
        $this->assertEquals('ABC', $userProfile->getZipCode());
    }

    public function testCity()
    {
        $userProfile = new UserProfile();
        $this->assertNull($userProfile->getCity());

        $userProfile->setCity("PARIS");
        $this->assertEquals("PARIS", $userProfile->getCity());
    }

    public function testReceiveMail()
    {
        $userProfile = new UserProfile();

        $this->assertFalse($userProfile->getReceiveMail());

        $userProfile->setReceiveMail(true);
        $this->assertTrue($userProfile->getReceiveMail());

        $userProfile->setReceiveMail(false);
        $this->assertFalse($userProfile->getReceiveMail());
    }

    public function testIsLockedOut()
    {
        $userProfile = new UserProfile();

        $this->assertFalse($userProfile->getIsLockedOut());

        $userProfile->setIsLockedOut(true);
        $this->assertTrue($userProfile->getIsLockedOut());

        $userProfile->setIsLockedOut(false);
        $this->assertFalse($userProfile->getIsLockedOut());
    }

    public function testIsSso()
    {
        $userProfile = new UserProfile();

        $this->assertFalse($userProfile->getIsSso());

        $userProfile->setIsSso(true);
        $this->assertTrue($userProfile->getIsSso());

        $userProfile->setIsSso(false);
        $this->assertFalse($userProfile->getIsSso());
    }

    public function testTestEndDate()
    {
        $userProfile = new UserProfile();

        $this->assertNull($userProfile->getTestEndDate());

        $now = new \DateTime();
        $userProfile->setTestEndDate($now);
        $this->assertEquals($now, $userProfile->getTestEndDate());

        $userProfile->setTestEndDate("31/12/2018");
        $this->assertEquals("31/12/2018", $userProfile->getTestEndDate()->format("d/m/Y"));

        $userProfile->setTestEndDate("2018-12-31");
        $this->assertEquals("31/12/2018", $userProfile->getTestEndDate()->format("d/m/Y"));

        $userProfile->setTestEndDate("2018_12_31");
        $this->assertNull($userProfile->getTestEndDate());
    }

    public function testGroup()
    {
        $userProfile = new UserProfile();

        $this->assertNull($userProfile->getGroup());

        $group = new Group();
        $userProfile->setGroup($group);
        $this->assertNotNull($userProfile->getGroup());
    }

    public function testPasswordToken()
    {
        $userProfile = new UserProfile();

        $this->assertNull($userProfile->getPasswordResetToken());
        $this->assertNull($userProfile->getPasswordResetTokenCreatedAt());

        $userProfile->setPasswordResetToken("token");
        $this->assertEquals("token", $userProfile->getPasswordResetToken());

        $now = new \DateTime();
        $userProfile->setPasswordResetTokenCreatedAt($now);
        $this->assertEquals($now, $userProfile->getPasswordResetTokenCreatedAt());
    }

    public function testCreatedAt()
    {
        $userProfile = new UserProfile();
        $this->assertNull($userProfile->getCreatedAt());
    }

    public function testUpdatedAt()
    {
        $userProfile = new UserProfile();
        $this->assertNull($userProfile->getUpdatedAt());
    }

    public function testGetConcurrencyArray()
    {
        $userProfile = new UserProfile();
        $this->assertEquals($this->emptyToArray, $userProfile->getConcurrencyArray());

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

        $group = new Group();
        $userProfile->setGroup($group);

        $resultArray = $userProfile->getConcurrencyArray();
        $resultGroup = $resultArray['group'];
        $resultTestEndDate = $resultArray['testEndDate']->format('d/m/Y');
        unset($resultArray['group']);
        unset($resultArray['testEndDate']);

        $this->assertEquals($this->fullToArray, $resultArray);
        $this->assertEquals($group, $resultGroup);
        $this->assertEquals('31/12/2020', $resultTestEndDate);
    }
}
