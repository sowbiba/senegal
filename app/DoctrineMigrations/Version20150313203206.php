<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150313203206 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // user
        $sqlUser = <<<SQL
CREATE TABLE IF NOT EXISTS user (
id INT(11) AUTO_INCREMENT NOT NULL,
username VARCHAR(255) NOT NULL,
password VARCHAR(128) NOT NULL,
algorithm VARCHAR(255) NOT NULL DEFAULT 'sha1',
salt VARCHAR(255) NULL,
lastname VARCHAR(255) NULL,
firstname VARCHAR(255) NULL,
address TEXT NULL,
phone VARCHAR(64) NULL,
email VARCHAR(512) NULL,
active TINYINT(1) NULL DEFAULT 1,
role_id INT(11),
token VARCHAR(255) NULL,
created_at DATETIME NOT NULL,
created_by INT DEFAULT NULL,
updated_at DATETIME NOT NULL,
updated_by INT DEFAULT NULL,
UNIQUE INDEX user_username_unique (username),
PRIMARY KEY(id),
KEY `user_role_id_idx` (`role_id`),
CONSTRAINT `user_role_id` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SQL;
        $this->addSql($sqlUser);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $sql = <<<SQL
DROP TABLE IF EXISTS user;
SQL;
        $this->addSql($sql);
    }
}
