<?php

namespace Senegal\ApiBundle\Tests;

use Doctrine\ORM\EntityManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseFunctionalTestCase extends WebTestCase
{
    protected static $companyClassName = 'Senegal\ApiBundle\Entity\Company';
    protected static $companyGroupClassName = 'Senegal\ApiBundle\Entity\CompanyGroup';
    protected static $contractClassName = 'Senegal\ApiBundle\Entity\Contract';
    protected static $contractStatusClassName = 'Senegal\ApiBundle\Entity\ContractStatus';
    protected static $contractSetClassName = 'Senegal\ApiBundle\Entity\ContractSet';
    protected static $contractSetIdentityClassName = 'Senegal\ApiBundle\Entity\ContractSetIdentity';
    protected static $concurrencyClassName = 'Senegal\ApiBundle\Entity\ConcurrencyLock';
    protected static $groupClassName = 'Senegal\ApiBundle\Entity\Group';
    protected static $obsContractClassName = 'Senegal\ApiBundle\Entity\ObsContract';
    protected static $productLineClassName = 'Senegal\ApiBundle\Entity\ProductLine';
    protected static $rapprochementSetClassName = 'Senegal\ApiBundle\Entity\RapprochementSet';
    protected static $roleClassName = 'Senegal\ApiBundle\Entity\Role';
    protected static $userClassName = 'Senegal\ApiBundle\Entity\User';
    protected static $userProfileClassName = 'Senegal\ApiBundle\Entity\UserProfile';
    protected static $versionClassName = 'Senegal\ApiBundle\Entity\Version';
    protected static $zoneClassName = 'Senegal\ApiBundle\Entity\Zone';
    protected static $fieldClassName = 'Senegal\ApiBundle\Entity\Field';

    protected static $companyDataFixtures = 'Senegal\ApiBundle\DataFixtures\ORM\Company\LoadCompanyData';
    protected static $companyGroupDataFixtures = 'Senegal\ApiBundle\DataFixtures\ORM\CompanyGroup\LoadCompanyGroupData';
    protected static $contractDataFixtures = 'Senegal\ApiBundle\DataFixtures\ORM\Contract\LoadContractData';
    protected static $contractStatusDataFixtures = 'Senegal\ApiBundle\DataFixtures\ORM\ContractStatus\LoadContractStatusData';
    protected static $contractSetDataFixtures = 'Senegal\ApiBundle\DataFixtures\ORM\ContractSet\LoadContractSetData';
    protected static $contractSetIdentityDataFixtures = 'Senegal\ApiBundle\DataFixtures\ORM\ContractSetIdentity\LoadContractSetIdentityData';
    protected static $concurrencyDataFixtures = 'Senegal\ApiBundle\DataFixtures\ORM\Concurrency\LoadConcurrencyData';
    protected static $groupDataFixtures = 'Senegal\ApiBundle\DataFixtures\ORM\Group\LoadGroupData';
    protected static $obsContractDataFixtures = 'Senegal\ApiBundle\DataFixtures\ORM\ObsContract\LoadObsContractData';
    protected static $productLineDataFixtures = 'Senegal\ApiBundle\DataFixtures\ORM\ProductLine\LoadProductLineData';
    protected static $rapprochementSetDataFixtures = 'Senegal\ApiBundle\DataFixtures\ORM\RapprochementSet\LoadRapprochementSetData';
    protected static $roleDataFixtures = 'Senegal\ApiBundle\DataFixtures\ORM\Role\LoadRoleData';
    protected static $userDataFixtures = 'Senegal\ApiBundle\DataFixtures\ORM\User\LoadUserData';
    protected static $versionDataFixtures = 'Senegal\ApiBundle\DataFixtures\ORM\Version\LoadVersionData';
    protected static $zoneDataFixtures = 'Senegal\ApiBundle\DataFixtures\ORM\Zone\LoadZoneData';
    protected static $fieldDataFixtures = 'Senegal\ApiBundle\DataFixtures\ORM\Field\LoadFieldData';

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
