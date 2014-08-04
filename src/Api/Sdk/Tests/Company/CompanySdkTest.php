<?php
namespace Api\Sdk\Tests\Company;

use Api\Sdk\Tests\SdkTestCase;

class CompanySdkTest extends SdkTestCase
{
    public function testGetCompanies()
    {
        $companies = $this->getSdk("company")->getAll();
        foreach ($companies as $company) {
            $this->assertInstanceOf('Api\Sdk\Model\Company', $company);
        }
    }
}
