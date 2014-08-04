<?php
namespace Api\Sdk\Tests\ProductLine;

use Api\Sdk\Tests\SdkTestCase;

class ProductLineSdkTest extends SdkTestCase
{

    public function testGetById()
    {
        $productLine = $this->getSdk("productLine")->getById(5);

        $this->assertInstanceOf('Api\Sdk\Model\ProductLine', $productLine);
        $this->assertEquals(5, $productLine->getId());
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testGetByIdWithBadParameter()
    {
        $this->getSdk("productLine")->getById("Assurance vie");
    }

    public function testGetAll()
    {
        $productLines = $this->getSdk("productLine")->getAll();
        foreach ($productLines as $productLine) {
            $this->assertInstanceOf('Api\Sdk\Model\ProductLine', $productLine);
        }
    }

    /**
     * Tests that root chapter's id equals given chapter's id and children are correctly set
     */
    public function testGetChapterTree()
    {
        $productLine = $this->getSdk("productLine")->getById(499);
        $firstChapterTree = $this->getSdk("productLine")->getChapterTree($productLine);

        // Test root node
        $this->assertEquals($productLine->getId(), $firstChapterTree->getId());
        $this->assertEquals(2, count($firstChapterTree->getChildren()));

    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testGetChapterTreeWithBadParameter()
    {
        $productLine = $this->getSdk("productLine")->getById(499);
        $productLine->setId("proout");
        $this->getSdk("productLine")->getChapterTree($productLine);
    }

    /**
     * Test that not integer parameter throws exception
     *
     * @expectedException BadMethodCallException
     */
    public function testGetFieldIdsWithBadParameterThrowException()
    {
        $onlyEnabledFieldsIds = false;

        $this->getSdk("productLine")->getFieldIds('TEST', $onlyEnabledFieldsIds);
    }

    /**
     * Test that parameter 0 throws exception
     *
     * @expectedException BadMethodCallException
     */
    public function testGetFieldIdsWithZeroThrowException()
    {
        $onlyEnabledFieldsIds = false;

        $this->getSdk("productLine")->getFieldIds(0, $onlyEnabledFieldsIds);
    }

    /**
     * Test that we obtain an array with fields with the product line id 20
     */
    public function testGetFieldIdsWithGoodParameterSuccess()
    {
        $onlyEnabledFieldsIds = false;

        $fieldsIds = $this->getSdk("productLine")->getFieldIds(20, $onlyEnabledFieldsIds);

        $this->assertCount(3, $fieldsIds);
        $this->assertTrue(in_array(6, $fieldsIds));
        $this->assertTrue(in_array(2, $fieldsIds));
        $this->assertTrue(in_array(3, $fieldsIds));
    }

}
