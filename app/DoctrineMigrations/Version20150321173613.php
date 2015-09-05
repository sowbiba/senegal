<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150321173613 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // type_block
        $sqlTypeBlock = <<<SQL
CREATE TABLE IF NOT EXISTS type_block (
id INT(11) AUTO_INCREMENT NOT NULL,
name VARCHAR(255) NOT NULL,
created_at DATETIME NOT NULL,
created_by INT DEFAULT NULL,
updated_at DATETIME NOT NULL,
updated_by INT DEFAULT NULL,
UNIQUE INDEX type_block_name_unique (name),
PRIMARY KEY(id)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SQL;
        $this->addSql($sqlTypeBlock);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $sql = <<<SQL
DROP TABLE IF EXISTS type_block;
SQL;
        $this->addSql($sql);
    }
}
