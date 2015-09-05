<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150321174217 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // page_has_block
        $sqlPageHasBlock = <<<SQL
CREATE TABLE IF NOT EXISTS page_has_block (
page_id INT(11),
block_id INT(11),
position INT(11) NULL DEFAULT 0,
created_at DATETIME NOT NULL,
created_by INT DEFAULT NULL,
updated_at DATETIME NOT NULL,
updated_by INT DEFAULT NULL,
PRIMARY KEY(page_id, block_id),
KEY `page_has_block_page_id_idx` (`page_id`),
KEY `page_has_block_block_id_idx` (`block_id`),
CONSTRAINT `page_has_block_page_id` FOREIGN KEY (`page_id`) REFERENCES `page` (`id`) ON DELETE CASCADE,
CONSTRAINT `page_has_block_block_id` FOREIGN KEY (`block_id`) REFERENCES `block` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SQL;
        $this->addSql($sqlPageHasBlock);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $sql = <<<SQL
DROP TABLE IF EXISTS page_has_block;
SQL;
        $this->addSql($sql);
    }
}
