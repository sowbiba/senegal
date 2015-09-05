<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150321173251 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // page
        $sqlPage = <<<SQL
CREATE TABLE IF NOT EXISTS page (
id INT(11) AUTO_INCREMENT NOT NULL,
slug VARCHAR(255) NOT NULL,
type_page_id INT(11),
created_at DATETIME NOT NULL,
created_by INT DEFAULT NULL,
updated_at DATETIME NOT NULL,
updated_by INT DEFAULT NULL,
UNIQUE INDEX page_slug_unique (slug),
PRIMARY KEY(id),
KEY `page_type_page_id_idx` (`type_page_id`),
CONSTRAINT `page_type_page_id` FOREIGN KEY (`type_page_id`) REFERENCES `type_page` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SQL;
        $this->addSql($sqlPage);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $sql = <<<SQL
DROP TABLE IF EXISTS page;
SQL;
        $this->addSql($sql);
    }
}
