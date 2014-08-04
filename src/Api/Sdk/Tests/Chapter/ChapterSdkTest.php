<?php
namespace Api\Sdk\Tests\Chapter;

use Api\Sdk\Tests\SdkTestCase;

class ChapterSdkTest extends SdkTestCase
{

    public function testGetChapterFieldsOk()
    {
        $productLine = $this->getSdk("productLine")->getById(499);
        // In our data, root chapter id = product line id...
        $chapter = $productLine->getChapterTree(499);
        $fields  = $this->getSdk("chapter")->getChapterFields($chapter);

        $this->assertInternalType('array', $fields);
        $this->assertEquals(6, count($fields));
        // test can convert this array into an array of field objects
        foreach ($this->getSdk("chapter")->convertFields($fields) as $field) {
            $this->assertInstanceOf('Api\Sdk\Model\Field', $field);
        }

    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testGetChapterFieldsWithBadParameter()
    {
        $productLine = $this->getSdk("productLine")->getById(499);
        // In our data, root chapter id = product line id...
        $chapter     = $productLine->getChapterTree(499);
        $chapter->setId("un");
        $this->getSdk("chapter")->getChapterFields($chapter);
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testGetProductLineWithBadParameterThrowsException()
    {
        $productLine = $this->getSdk("productLine")->getById(999);
        // In our data, root chapter id = product line id...
        $chapter     = $productLine->getChapterTree(999);

        $this->getSdk('chapter')->getProductLine($chapter);
    }

    public function testGetProductLineWithChapterReturnProductLine()
    {
        $productLine         = $this->getSdk("productLine")->getById(499);
        // In our data, root chapter id = product line id...
        $chapter             = $productLine->getChapterTree(499);
        $productLineExpected = $this->getSdk('chapter')->getProductLine($chapter);

        $this->assertSame($productLine->getId(), $productLineExpected->getId());
        $this->assertInstanceOf('Api\Sdk\Model\ProductLine', $productLineExpected);
    }

}
