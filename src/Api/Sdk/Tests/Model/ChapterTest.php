<?php

namespace Api\Sdk\Tests\Model;

use Api\Sdk\Model\Chapter;
use Api\Sdk\Model\Field;
use Api\Sdk\Tests\SdkTestCase;

class ChapterTest extends SdkTestCase
{

    /**
     * Test the table view return
     *
     * Representation of the test case :
     *  ___________________________________________________________________
     * |                   | Field $field1 | Field $field2 | Field $field4 |
     * |___________________|_______________|_______________|_______________|
     * | Chapter $chapter1 | Field $field1 |      NULL     |      NULL     |
     * |___________________|_______________|_______________|_______________|
     * | Chapter $chapter2 |      NULL     | Field $field2 |      NULL     |
     * |___________________|_______________|_______________|_______________|
     * | Chapter $chapter3 |      NULL     | Field $field3 | Field $field4 |
     * |___________________|_______________|_______________|_______________|
     * | Chapter $chapter4 |      NULL     | Field $field6 |      NULL     |
     * |                   |               | Field $field5 |               |
     * |___________________|_______________|_______________|_______________|
     *
     */
    public function dataGetTableView()
    {
        return array(
            [// display all rows and columns
                [1 => 'not null', 2 => 3, 3 => null, 4 => "", 5 => "not null", 6 => "test"],
                [],
                [1 => false, 2 => false, 3 => false, 4 => false ],
                [1 => false, 2 => false, 3 => false ],
            ],
            [// hide first column
                [1 => '#NA', 2 => 3, 3 => null, 4 => "", 5 => "not null", 6 => "test"],
                [],
                [1 => false, 2 => false, 3 => false, 4 => false ],
                [1 => true, 2 => false, 3 => false ],
            ],
            [ // hide last rows
                [1 => 'not null', 2 => 3, 3 => null, 4 => "", 5 => "#NA", 6 => "#NA"],
                [4],
                [1 => false, 2 => false, 3 => false, 4 => true],
                [1 => false, 2 => false, 3 => false],
            ],
            [ // hide second column and the last rows
                [1 => 'not null', 2 => "#NA", 3 => "#NA", 4 => "", 5 => "#NA", 6 => "#NA"],
                [4],
                [1 => false, 2 => false, 3 => false, 4 => true],
                [1 => false, 2 => true, 3 => false],
            ],
        );
    }

    /**
     * Test the table view return
     *
     * Representation of the test case :
     *  ___________________________________________________________________
     * |                   | Field $field1 | Field $field2 | Field $field4 |
     * |___________________|_______________|_______________|_______________|
     * | Chapter $chapter1 | Field $field1 |      NULL     |      NULL     |
     * |___________________|_______________|_______________|_______________|
     * | Chapter $chapter2 |      NULL     | Field $field2 |      NULL     |
     * |___________________|_______________|_______________|_______________|
     * | Chapter $chapter3 |      NULL     | Field $field3 | Field $field4 |
     * |___________________|_______________|_______________|_______________|
     * | Chapter $chapter4 |      NULL     | Field $field6 |      NULL     |
     * |                   |               | Field $field5 |               |
     * |___________________|_______________|_______________|_______________|
     *
     * @dataProvider dataGetTableView
     *
     */
    public function testGetTableView($values, $chaptersIdToHide, $rowsIsNaExpected, $colsIsNaExpected)
    {
        $chapter = new Chapter($this->getSdk("chapter"), ['isTable' => true]);

        $subChapter1 = new Chapter($this->getSdk("chapter"), ['name' => 'chapter1', 'id' => 1]);
        $subChapter2 = new Chapter($this->getSdk("chapter"), ['name' => 'chapter2', 'id' => 2]);
        $subChapter3 = new Chapter($this->getSdk("chapter"), ['name' => 'chapter3', 'id' => 3]);
        $subChapter4 = new Chapter($this->getSdk("chapter"), ['name' => 'chapter4', 'id' => 4]);

        // Table's row display result :
        // chapter1 | Field $field1 | null | null
        $field1 = new Field($this->getSdk('field'), ['name' => 'field1', 'id' => 1]);
        $subChapter1->setFields([$field1]);

        // Table's row display result :
        // chapter2 | null | Field $field2 | null
        $field2 = new Field($this->getSdk('field'), ['name' => 'field2', 'id' => 2]);
        $subChapter2->setFields([$field2]);

        // Table's row display result :
        // chapter3 | null | Field $field3 | Field $field4
        $field3 = new Field($this->getSdk('field'), ['name' => 'field2', 'id' => 3]);
        $field4 = new Field($this->getSdk('field'), ['name' => 'field3', 'id' => 4]);
        $subChapter3->setFields([$field3, $field4]);

        // Table's row display result :
        // chapter4 | null | Field $field6 Field $field5 | null
        $field5 = new Field($this->getSdk('field'), ['name' => 'field2', 'id' => 5]);
        $field6 = new Field($this->getSdk('field'), ['name' => 'FIELD2', 'id' => 6]);
        $subChapter4->setFields([$field5, $field6]);

        $chapter->setChildren([$subChapter1, $subChapter2, $subChapter3, $subChapter4]);

        $expected = [
            [$field1, $field2, $field4],
            [$subChapter1, array($field1), null, null],
            [$subChapter2, null, array($field2), null],
            [$subChapter3, null, array($field3), array($field4)],
            [$subChapter4, null, array($field5, $field6), null],
            "rowsIsNa" => $rowsIsNaExpected,
            "colsIsNa" => $colsIsNaExpected
        ];

        $this->assertSame($expected, $chapter->getTableView($values, $chaptersIdToHide));
    }
}
