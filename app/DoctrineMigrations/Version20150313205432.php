<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150313205432 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // forfait_has_type_page
        $sqlForfaitHasTypePage = <<<SQL
CREATE TABLE IF NOT EXISTS forfait_has_type_page (
forfait_id INT(11),
type_page_id INT(11),
allowed_page_number INT NULL,
PRIMARY KEY(forfait_id, type_page_id),
KEY `forfait_has_type_page_forfait_id_idx` (`forfait_id`),
KEY `forfait_has_type_page_type_page_id_idx` (`type_page_id`),
CONSTRAINT `forfait_has_type_page_forfait_id` FOREIGN KEY (`forfait_id`) REFERENCES `forfait` (`id`) ON DELETE CASCADE,
CONSTRAINT `forfait_has_type_page_type_page_id` FOREIGN KEY (`type_page_id`) REFERENCES `type_page` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SQL;
        $this->addSql($sqlForfaitHasTypePage);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $sql = <<<SQL
DROP TABLE IF EXISTS forfait_has_type_page;
SQL;
        $this->addSql($sql);
    }
}
