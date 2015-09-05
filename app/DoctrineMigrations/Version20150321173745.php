<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150321173745 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // block
        $sqlBlock = <<<SQL
CREATE TABLE IF NOT EXISTS block (
id INT(11) AUTO_INCREMENT NOT NULL,
name VARCHAR(255) NOT NULL,
type_block_id INT(11),
created_at DATETIME NOT NULL,
created_by INT DEFAULT NULL,
updated_at DATETIME NOT NULL,
updated_by INT DEFAULT NULL,
UNIQUE INDEX block_name_unique (name),
PRIMARY KEY(id),
KEY `block_type_block_id_idx` (`type_block_id`),
CONSTRAINT `block_type_block_id` FOREIGN KEY (`type_block_id`) REFERENCES `type_block` (`id`) ON DELETE SET NULL
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
DROP TABLE IF EXISTS block;
SQL;
        $this->addSql($sql);
    }
}
