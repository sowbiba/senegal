<?php
/**
 * Author: Florent Coquel
 * Date: 17/09/13
 */

namespace Api\Sdk\Tests;

use Api\Sdk\Chapter\ChapterSdk;
use Api\Sdk\Chapter\Connector\Data\ChapterDataConnector;
use Api\Sdk\Chapter\Connector\ChapterConnector;
use Api\Sdk\Company\CompanySdk;
use Api\Sdk\Company\Connector\Data\CompanyDataConnector;
use Api\Sdk\Company\Connector\CompanyConnector;
use Api\Sdk\CompanyGroup\Connector\Data\CompanyGroupDataConnector;
use Api\Sdk\CompanyGroup\CompanyGroupSdk;
use Api\Sdk\CompanyGroup\Connector\CompanyGroupConnector;
use Api\Sdk\CompanyType\CompanyTypeSdk;
use Api\Sdk\CompanyType\Connector\CompanyTypeConnector;
use Api\Sdk\CompanyType\Connector\Data\CompanyTypeDataConnector;
use Api\Sdk\Contract\Connector\Data\ContractDataConnector;
use Api\Sdk\Contract\Connector\ContractConnector;
use Api\Sdk\Contract\ContractSdk;
use Api\Sdk\Document\Connector\Data\DocumentDataConnector;
use Api\Sdk\Document\Connector\DocumentConnector;
use Api\Sdk\Document\DocumentSdk;
use Api\Sdk\Field\Connector\Data\FieldDataConnector;
use Api\Sdk\Field\Connector\FieldConnector;
use Api\Sdk\Field\FieldSdk;
use Api\Sdk\Market\Connector\Data\MarketDataConnector;
use Api\Sdk\Market\Connector\MarketConnector;
use Api\Sdk\Market\MarketSdk;
use Api\Sdk\Media\Connector\Data\MediaDataConnector;
use Api\Sdk\Media\Connector\MediaConnector;
use Api\Sdk\Media\MediaSdk;
use Api\Sdk\Mediator\ConnectorMediator;
use Api\Sdk\Mediator\SdkMediator;
use Api\Sdk\ProductLine\Connector\Data\ProductLineDataConnector;
use Api\Sdk\ProductLine\Connector\ProductLineConnector;
use Api\Sdk\ProductLine\ProductLineSdk;
use Api\Sdk\Revision\Connector\Data\RevisionDataConnector;
use Api\Sdk\Revision\Connector\RevisionConnector;
use Api\Sdk\Revision\RevisionSdk;
use Api\Sdk\User\Connector\Data\UserDataConnector;
use Api\Sdk\User\Connector\UserConnector;
use Api\Sdk\User\UserSdk;
use Api\Sdk\Role\RoleSdk;
use Api\Sdk\Role\Connector\RoleConnector;
use Api\Sdk\Role\Connector\Data\RoleDataConnector;
use Api\Sdk\User\Connector\Data\UserPropelConnector; // Alias class of UserDataConnector

//use Api\Sdk\Connector\DataConnector;

class SdkTestCase extends \PHPUnit_Framework_TestCase
{
    private $sdkMediator;
    private $connectorMediator;
    private $connectors;
    private $sdks;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->buildConnectors();
        $this->buildSdks();
    }

    /**
     * Build all sdk, and populate sdk manager
     */
    private function buildConnectors()
    {
        $this->connectors = array(
            "chapter"      => new ChapterConnector(array(new ChapterDataConnector())),
            "company"      => new CompanyConnector(array(new CompanyDataConnector())),
            "companyGroup" => new CompanyGroupConnector(array(new CompanyGroupDataConnector())),
            "companyType"  => new CompanyTypeConnector(array(new CompanyTypeDataConnector())),
            "contract"     => new ContractConnector(array(new ContractDataConnector())),
            "document"     => new DocumentConnector(array(new DocumentDataConnector())),
            "field"        => new FieldConnector(array(new FieldDataConnector())),
            "market"       => new MarketConnector(array(new MarketDataConnector())),
            "media"        => new MediaConnector(array(new MediaDataConnector())),
            "productLine"  => new ProductLineConnector(array(new ProductLineDataConnector())),
            "revision"     => new RevisionConnector(array(new RevisionDataConnector())),
            "user"         => new UserConnector(array(new UserDataConnector())),
            "role"         => new RoleConnector(array(new RoleDataConnector())),
            "userPropel"   => new UserConnector(array(new UserPropelConnector())),
        );

        $logger = $this->getMockBuilder('Monolog\Logger')
            ->disableOriginalConstructor()
            ->getMock();

        $this->connectorMediator = new ConnectorMediator($logger);
        $this->connectorMediator->setColleagueList($this->connectors);
    }

    /**
     * Build all sdk, and populate sdk manager
     */
    private function buildSdks()
    {

        $this->sdks = array(
            "chapter"      => new ChapterSdk($this->getConnector("chapter")),
            "company"      => new CompanySdk($this->getConnector("company")),
            "companyGroup" => new CompanyGroupSdk($this->getConnector("companyGroup")),
            "companyType"  => new CompanyTypeSdk($this->getConnector("companyType")),
            "contract"     => new ContractSdk($this->getConnector("contract")),
            "document"     => new DocumentSdk($this->getConnector("document")),
            "field"        => new FieldSdk($this->getConnector("field")),
            "market"       => new MarketSdk($this->getConnector("market")),
            "media"        => new MediaSdk($this->getConnector("media")),
            "productLine"  => new ProductLineSdk($this->getConnector("productLine")),
            "revision"     => new RevisionSdk($this->getConnector("revision")),
            "user"         => new UserSdk($this->getConnector("user")),
            "role"         => new RoleSdk($this->getConnector("role")),
        );

        $logger = $this->getMockBuilder('Monolog\Logger')
            ->disableOriginalConstructor()
            ->getMock();

        $this->sdkMediator = new SdkMediator($logger);
        $this->sdkMediator->setColleagueList($this->sdks);
    }

    /**
     * @param $name
     *
     * @return SdkInterface
     */
    public function getSdk($name)
    {
        //return $this->sdkMediator->getSdk($name);
        return $this->sdks[$name];
    }

    /**
     * @param $name
     *
     * @return SdkInterface
     */
    public function getConnector($name)
    {
        //return $this->connectorMediator->getColleague($name);
        return $this->connectors[$name];
    }

    /**
     * @param string $classname
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockWithoutConstructor($classname)
    {
        return $this->getMockBuilder($classname)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
