<?php
namespace Api\Sdk\Tests\Model;

use Api\Sdk\Model\ProductLine;
use Api\Sdk\Tests\SdkTestCase;

class ProductLineTest extends SdkTestCase
{
    public function testCreateFromArray()
    {
        $productLineData = [
            'id'              => 5,
            'name'            => 'Assurance vie',
            'rootChapterId'   => 500,
            'isRevisionable'  => true,
        ];

        // Create a fake contract for testing
        $productLine = new ProductLine($this->getSdk("productLine"), $productLineData);

        // Assert
        $this->assertEquals($productLine->getId(), $productLineData['id']);
        $this->assertEquals($productLine->getName(), $productLineData['name']);
        $this->assertEquals($productLine->isRevisionable(), $productLineData['isRevisionable']);

        $this->assertSame($productLine->toArray(), [
            'id'               => 5,
            'name'             => 'Assurance vie',
            'chapitresroot_id' => 500,
            'is_revisionable'  => 1,
        ]);
    }

}
