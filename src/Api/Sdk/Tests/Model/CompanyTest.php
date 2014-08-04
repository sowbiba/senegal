<?php
namespace Api\Sdk\Tests\Model;

use Api\Sdk\Model\Company;
use Api\Sdk\Tests\SdkTestCase;

class CompanyTest extends SdkTestCase
{
    public function testCreateFromArray()
    {
        $companyData = [
            'id'              => 1,
            'name'            => 'ACTE VIE',
        ];

        // Create a fake contract for testing
        $company = new Company($this->getSdk("company"), $companyData);

        // Assert
        $this->assertEquals($company->getId(), $companyData['id']);
        $this->assertEquals($company->getName(), $companyData['name']);
        $this->assertEquals($company, $companyData['name']);
    }

}
