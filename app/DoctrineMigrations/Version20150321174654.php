<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150321174654 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // element
        $sqlBlock = <<<SQL
CREATE TABLE IF NOT EXISTS element (
id INT(11) AUTO_INCREMENT NOT NULL,
type_element_id INT(11),
content TEXT NULL,
legend TEXT NULL,
created_at DATETIME NOT NULL,
created_by INT DEFAULT NULL,
updated_at DATETIME NOT NULL,
updated_by INT DEFAULT NULL,
PRIMARY KEY(id),
KEY `element_type_element_id_idx` (`type_element_id`),
CONSTRAINT `element_type_element_id` FOREIGN KEY (`type_element_id`) REFERENCES `type_element` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SQL;
        $this->addSql($sqlBlock);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $sql = <<<SQL
DROP TABLE IF EXISTS element;
SQL;
        $this->addSql($sql);
    }
}
