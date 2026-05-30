<?php

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version202505300900000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE user_users_favorite_product (
                user_id    INT UNSIGNED NOT NULL,
                product_id INT UNSIGNED NOT NULL,
                PRIMARY KEY (user_id, product_id),
                INDEX IDX_UFP_user    (user_id),
                INDEX IDX_UFP_product (product_id),
                CONSTRAINT FK_UFP_user    FOREIGN KEY (user_id)    REFERENCES user_users      (id) ON DELETE CASCADE,
                CONSTRAINT FK_UFP_product FOREIGN KEY (product_id) REFERENCES product_product (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE user_users_favorite_product');
    }
}