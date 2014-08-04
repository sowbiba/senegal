<?php
namespace Api\Sdk\Tests\CompanyGroup;

use Api\Sdk\Tests\SdkTestCase;

class CompanyGroupSdkTest extends SdkTestCase
{
    public function testGetCompanyGroups()
    {
        $companyGroups = $this->getSdk("companyGroup")->getAll();
        foreach ($companyGroups as $companyGroup) {
            $this->assertInstanceOf('Api\Sdk\Model\CompanyGroup', $companyGroup);
        }
    }
}
