<?php
/**
 * Date: 24/10/13
 */

namespace Api\Sdk\Tests\ProductLine\Connector\Doctrine;

use Api\Sdk\Chapter\ChapterSdk;
use Api\Sdk\Field\FieldSdk;
use Api\Sdk\Mediator\SdkMediator;
use Api\Sdk\Tests\SdkTestCase;
use Api\Sdk\ProductLine\Connector\Doctrine\ProductLineDoctrineConnector;
use Api\Sdk\Model\ProductLine;
use Api\Sdk\Model\Chapter;
use Api\Sdk\Model\Field;

class ProductLineDoctrineConnectorTest extends SdkTestCase
{
    /**
     *
     * @var Api\Sdk\ProductLine\Connector\Doctrine\ProductLineDoctrineConnector
     */
    private $connector;

    /**
     *
     * @var Doctrine\ORM\EntityManager mock of entity manager
     */
    private $connection;

    public function setUp()
    {
        // Mock doctrine connection to not call database
        $this->connection = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        // Mock entity manager to instanciate product line connector
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $em->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($this->connection));

        $repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repository));

        $this->connector = new ProductLineDoctrineConnector($em);
    }

    public function fieldIdsGetterMethodsData()
    {
        $productLine = new ProductLine($this->getSdk('productLine'));
        $productLine->setId(1);

        $targetStmt = 'SELECT t.target_field_id field_id
FROM data_entry_rule_to_target_field AS t
INNER JOIN data_entry_rule AS r
ON t.rule_id = r.id
WHERE r.gammes_id = ?
;';
        $sourceStmt = 'SELECT DISTINCT(derco.source_field_id) field_id
FROM data_entry_rule_criteria_or AS derco
INNER JOIN data_entry_rule_criteria_and AS derca
ON derco.criteria_and_id = derca.id
INNER JOIN data_entry_rule der
ON derca.rule_id=der.id
WHERE der.gammes_id = ?
;';

        return [
            [$productLine, [1, 2], $targetStmt, 'getTargetFieldIds'],
            [$productLine, [1, 2], $sourceStmt, 'getSourceFieldIds'],
        ];
    }

    /**
     * Test than field ids getter methods in data provider execute
     * the right statement query and return the right data in right format
     *
     * @dataProvider fieldIdsGetterMethodsData
     *
     * @param Api\Sdk\Model\ProductLine $productLine a productline for test
     * @param int[]                     $fieldIds    fields ids supposed to be returned
     * @param string                    $stmt        statement supposed to be executed
     * @param string                    $ruleMethod  fields ids getter method in ProductLineDoctrineConnector
     */
    public function testFieldIdsGetterMethodsExecuteRightQuery($productLine, $fieldIds, $stmt, $ruleMethod)
    {
        // Mock PDOStatement
        $pdoStmtMock =
            $this->getMockBuilder(
                'Api\Sdk\Tests\ProductLine\Connector\Doctrine\PDOStatementMock'
            )
            ->getMock();

        // Mock PDOStatement::fetch for each fields to return expected result
        $i=1;

        foreach ($fieldIds as $fieldId) {
            $pdoStmtMock->expects($this->at($i))
            ->method('fetch')
            ->will($this->returnValue(['field_id' => $fieldId]));
            $i++;
        }

        // Check that PDOStatement::execute receive the right bind parameter
        $pdoStmtMock->expects($this->once())
            ->method('execute')
            ->with([$productLine->getId()]);

        // Check that PDOStatement::prepare execute the right sql statement
        $this->connection->expects($this->once())
            ->method('prepare')
            ->with($stmt)
            ->will($this->returnValue($pdoStmtMock));

        // Launch method to test
        $targetFieldIds = $this->connector->$ruleMethod($productLine);

        // Assert that returns the two field ids in right format
        $this->assertSame($targetFieldIds, $fieldIds);
    }

    public function dataGetChaptersIdToHide()
    {
        $na = Field::VALUE_NA;

        return array(
            [
                [
                    1 => "champ #1",
                    2 => "champ #2",
                    3 => "champ #3",
                    4 => "champ #4",
                    5 => "champ #5",
                    6 => "champ #6",
                    7 => "champ #7"
                ],
                array(7)
            ],
            [
                [
                    1 => "champ #1",
                    2 => $na,
                    3 => "champ #3",
                    4 => $na,
                    5 => "champ #5",
                    6 => "champ #6",
                    7 => "champ #7"
                ],
                array(4, 7)
            ],
            [
                [
                    1 => $na,
                    2 => $na,
                    3 => $na,
                    4 => $na,
                    5 => $na,
                    6 => $na,
                    7 => $na
                ],
                array(1, 2, 3, 4, 5, 6, 7, 9)
            ],
        );
    }

    /**
     * @dataProvider dataGetChaptersIdToHide
     *
     * Chapter's arborescence :
     *
     * Root chapter #0--------------
     * --Chapter #1-----------------
     * ----Chapter #2---------------
     * ------Field #1---------------
     * ------Field #2---------------
     * --Chapter #3-----------------
     * ----Field #3-----------------
     * ----Chapter #4---------------
     * ------Field #4---------------
     * --Chapter #5-----------------
     * ----Field #5-----------------
     * --Chapter #6 (table)---------
     * ----Field #6-----------------
     * --Chapter #7 (table)---------
     * --Chapter #8-----------------
     * ----Field #8 (no supported)--
     * ----Chapter #9---------------
     * ------Field #7---------------
     */
    public function testGetChaptersIdToHide($values, $chaptersIdToHide)
    {
        // Build sdks
        $sdks = array(
            "chapter" => new ChapterSdk($this->connector),
            "field"   => new FieldSdk($this->connector)
        );

        $logger = $this->getMockBuilder('Monolog\Logger')
            ->disableOriginalConstructor()
            ->getMock();

        $sdkMediator = new SdkMediator($logger);
        $sdkMediator->setSdkList($sdks);
        $fieldSdk   = $sdkMediator->getSdk('field');
        $chapterSdk = $sdkMediator->getSdk('chapter');

        // Build all fields
        $field1 = new Field($fieldSdk, array('id' => 1, 'typeId' => 1));
        $field2 = new Field($fieldSdk, array('id' => 2, 'typeId' => 1));
        $field3 = new Field($fieldSdk, array('id' => 3, 'typeId' => 1));
        $field4 = new Field($fieldSdk, array('id' => 4, 'typeId' => 1));
        $field5 = new Field($fieldSdk, array('id' => 5, 'typeId' => 1));
        $field6 = new Field($fieldSdk, array('id' => 6, 'typeId' => 1));
        $field7 = new Field($fieldSdk, array('id' => 7, 'typeId' => 1));
        $field8 = new Field($fieldSdk, array('id' => 8, 'typeId' => 4));

        // Build chapter #2 (with field #1 and #2)
        $chapter2 = new Chapter($chapterSdk, array('id' => 2, 'isTable' => false));
        $field2->setChapter($chapter2);
        $chapter2->setFields(array($field1, $field2));

        // Build chapter #1 (with no field and sub-chapter #2)
        $chapter1 = new Chapter($chapterSdk, array('id' => 1, 'isTable' => false));
        $field1->setChapter($chapter2);
        $chapter1->setChildren(array($chapter2));

        // Build chapter #4 (with field #4)
        $chapter4 = new Chapter($chapterSdk, array('id' => 4, 'isTable' => false));
        $field4->setChapter($chapter4);
        $chapter4->setFields(array($field4));

        // Build chapter #3 (with field #3 and sub-chapter #4)
        $chapter3 = new Chapter($chapterSdk, array('id' => 3, 'isTable' => false));
        $field3->setChapter($chapter3);
        $chapter3->setChildren(array($chapter4));
        $chapter3->setFields(array($field3));

        // Build chapter #5 (with field #5)
        $chapter5 = new Chapter($chapterSdk, array('id' => 5, 'isTable' => false));
        $field5->setChapter($chapter5);
        $chapter5->setFields(array($field5));

        // Build chapter #6 (with field #6), this chapter is table
        $chapter6 = new Chapter($chapterSdk, array('id' => 6, 'isTable' => true));
        $field6->setChapter($chapter6);
        $chapter6->setFields(array($field6));

        // Build chapter #7 (with no field), this chapter is table
        $chapter7 = new Chapter($chapterSdk, array('id' => 7, 'isTable' => true));

        // Build chapter #9 (with field #7)
        $chapter9 = new Chapter($chapterSdk, array('id' => 9, 'isTable' => false));
        $field7->setChapter($chapter9);
        $chapter9->setFields(array($field7));

        // Build chapter #8 (with field #8 and sub-chapter #9)
        $chapter8 = new Chapter($chapterSdk, array('id' => 8, 'isTable' => false));
        $field8->setChapter($chapter8);
        $chapter8->setChildren(array($chapter9));
        $chapter8->setFields(array($field8));

        // Build root chapter #0 (with sub-chapter #1, #3, #5, #6, #7, #9)
        $chapter0 = new Chapter($chapterSdk, array('id' => 0, 'isTable' => false));
        $chapter0->setChildren(array($chapter1, $chapter3, $chapter5, $chapter6, $chapter7, $chapter8));

        $result = $this->connector->getChaptersIdToHide($values, $chapter0);

        $this->assertEquals($chaptersIdToHide, $result);
    }
}
