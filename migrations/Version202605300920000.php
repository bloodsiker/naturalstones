<?php

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version202605300920000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE user_users_delivery_address (
                id INT UNSIGNED AUTO_INCREMENT NOT NULL,
                user_id INT UNSIGNED NOT NULL,
                title VARCHAR(120) DEFAULT NULL,
                address LONGTEXT NOT NULL,
                is_default TINYINT(1) NOT NULL DEFAULT 0,
                created_at DATETIME NOT NULL,
                INDEX IDX_UD_ADDRESS_USER (user_id),
                INDEX IDX_UD_ADDRESS_DEFAULT (is_default),
                PRIMARY KEY(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        $this->addSql('
            ALTER TABLE user_users_delivery_address
            ADD CONSTRAINT FK_UD_ADDRESS_USER
            FOREIGN KEY (user_id) REFERENCES user_users (id)
            ON DELETE CASCADE
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE user_users_delivery_address');
    }
}
