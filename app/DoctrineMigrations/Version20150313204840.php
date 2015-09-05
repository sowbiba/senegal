<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150313204840 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // user_has_forfait
        $sqlUserHasForfait = <<<SQL
CREATE TABLE IF NOT EXISTS user_has_forfait (
user_id INT(11),
forfait_id INT(11),
PRIMARY KEY(user_id, forfait_id),
KEY `user_has_forfait_user_id_idx` (`user_id`),
KEY `user_has_forfait_forfait_id_idx` (`forfait_id`),
CONSTRAINT `user_has_forfait_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
CONSTRAINT `user_has_forfait_forfait_id` FOREIGN KEY (`forfait_id`) REFERENCES `forfait` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SQL;
        $this->addSql($sqlUserHasForfait);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $sql = <<<SQL
DROP TABLE IF EXISTS user_has_forfait;
SQL;
        $this->addSql($sql);
    }
}
