<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150321174851 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // block_has_element
        $sqlBlockHasElement = <<<SQL
CREATE TABLE IF NOT EXISTS block_has_element (
block_id INT(11),
element_id INT(11),
position INT(11) NULL DEFAULT 0,
created_at DATETIME NOT NULL,
created_by INT DEFAULT NULL,
updated_at DATETIME NOT NULL,
updated_by INT DEFAULT NULL,
PRIMARY KEY(block_id, element_id),
KEY `block_has_element_block_id_idx` (`block_id`),
KEY `block_has_element_element_id_idx` (`element_id`),
CONSTRAINT `block_has_element_block_id` FOREIGN KEY (`block_id`) REFERENCES `block` (`id`) ON DELETE CASCADE,
CONSTRAINT `block_has_element_element_id` FOREIGN KEY (`element_id`) REFERENCES `element` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SQL;
        $this->addSql($sqlBlockHasElement);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $sql = <<<SQL
DROP TABLE IF EXISTS block_has_element;
SQL;
        $this->addSql($sql);
    }
}
