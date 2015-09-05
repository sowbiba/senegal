<?php

namespace Senegal\BackBundle\Tests;

use Doctrine\ORM\EntityManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseFunctionalTestCase extends WebTestCase
{
    protected static $companyClassName = 'Senegal\BackBundle\Entity\Company';
    protected static $companyGroupClassName = 'Senegal\BackBundle\Entity\CompanyGroup';
    protected static $contractClassName = 'Senegal\BackBundle\Entity\Contract';
    protected static $contractStatusClassName = 'Senegal\BackBundle\Entity\ContractStatus';
    protected static $contractSetClassName = 'Senegal\BackBundle\Entity\ContractSet';
    protected static $contractSetIdentityClassName = 'Senegal\BackBundle\Entity\ContractSetIdentity';
    protected static $concurrencyClassName = 'Senegal\BackBundle\Entity\ConcurrencyLock';
    protected static $groupClassName = 'Senegal\BackBundle\Entity\Group';
    protected static $obsContractClassName = 'Senegal\BackBundle\Entity\ObsContract';
    protected static $productLineClassName = 'Senegal\BackBundle\Entity\ProductLine';
    protected static $rapprochementSetClassName = 'Senegal\BackBundle\Entity\RapprochementSet';
    protected static $roleClassName = 'Senegal\BackBundle\Entity\Role';
    protected static $userClassName = 'Senegal\BackBundle\Entity\User';
    protected static $userProfileClassName = 'Senegal\BackBundle\Entity\UserProfile';
    protected static $versionClassName = 'Senegal\BackBundle\Entity\Version';
    protected static $zoneClassName = 'Senegal\BackBundle\Entity\Zone';
    protected static $fieldClassName = 'Senegal\BackBundle\Entity\Field';

    protected static $companyDataFixtures = 'Senegal\BackBundle\DataFixtures\ORM\Company\LoadCompanyData';
    protected static $companyGroupDataFixtures = 'Senegal\BackBundle\DataFixtures\ORM\CompanyGroup\LoadCompanyGroupData';
    protected static $contractDataFixtures = 'Senegal\BackBundle\DataFixtures\ORM\Contract\LoadContractData';
    protected static $contractStatusDataFixtures = 'Senegal\BackBundle\DataFixtures\ORM\ContractStatus\LoadContractStatusData';
    protected static $contractSetDataFixtures = 'Senegal\BackBundle\DataFixtures\ORM\ContractSet\LoadContractSetData';
    protected static $contractSetIdentityDataFixtures = 'Senegal\BackBundle\DataFixtures\ORM\ContractSetIdentity\LoadContractSetIdentityData';
    protected static $concurrencyDataFixtures = 'Senegal\BackBundle\DataFixtures\ORM\Concurrency\LoadConcurrencyData';
    protected static $groupDataFixtures = 'Senegal\BackBundle\DataFixtures\ORM\Group\LoadGroupData';
    protected static $obsContractDataFixtures = 'Senegal\BackBundle\DataFixtures\ORM\ObsContract\LoadObsContractData';
    protected static $productLineDataFixtures = 'Senegal\BackBundle\DataFixtures\ORM\ProductLine\LoadProductLineData';
    protected static $rapprochementSetDataFixtures = 'Senegal\BackBundle\DataFixtures\ORM\RapprochementSet\LoadRapprochementSetData';
    protected static $roleDataFixtures = 'Senegal\BackBundle\DataFixtures\ORM\Role\LoadRoleData';
    protected static $userDataFixtures = 'Senegal\BackBundle\DataFixtures\ORM\User\LoadUserData';
    protected static $versionDataFixtures = 'Senegal\BackBundle\DataFixtures\ORM\Version\LoadVersionData';
    protected static $zoneDataFixtures = 'Senegal\BackBundle\DataFixtures\ORM\Zone\LoadZoneData';
    protected static $fieldDataFixtures = 'Senegal\BackBundle\DataFixtures\ORM\Field\LoadFieldData';

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }

    protected function truncateTables(array $classNames = [])
    {
        $em = $this->getEntityManager();

        $connection = $em->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->query('SET FOREIGN_KEY_CHECKS=0');

        foreach ($classNames as $className) {
            $connection->executeUpdate($dbPlatform->getTruncateTableSql($em->getClassMetadata($className)->getTableName()));
        }

        $connection->query('SET FOREIGN_KEY_CHECKS=1');
        $connection->close();
    }

    protected function assertResponse(Response $response, $statusCode = 200)
    {
        $this->assertEquals(
            $statusCode, $response->getStatusCode(),
            $response->getContent()
        );
    }

    protected function assertJsonResponse(Response $response, $statusCode = 200)
    {
        $this->assertEquals(
            $statusCode, $response->getStatusCode(),
            $response->getContent()
        );
        $this->assertTrue(
            $response->headers->contains('Content-Type', 'application/json'),
            $response->headers
        );
    }

    protected function assertXmlResponse(Response $response, $statusCode = 200)
    {
        $this->assertEquals(
            $statusCode, $response->getStatusCode(),
            $response->getContent()
        );
        $this->assertTrue(
            $response->headers->contains('Content-Type', 'text/xml; charset=UTF-8'),
            $response->headers
        );
    }
}
