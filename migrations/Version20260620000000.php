<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260620000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add promo code system: order_promo_code table, discount_promo and promo_code_id columns on order_order';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE order_promo_code (
            id INT UNSIGNED AUTO_INCREMENT NOT NULL,
            code VARCHAR(64) NOT NULL,
            type VARCHAR(10) NOT NULL DEFAULT \'fixed\',
            value DECIMAL(10,2) NOT NULL DEFAULT \'0.00\',
            expires_at DATETIME DEFAULT NULL,
            usage_limit INT DEFAULT NULL,
            usage_count INT NOT NULL DEFAULT 0,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL,
            UNIQUE INDEX UNIQ_promo_code_code (code),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE order_order
            ADD promo_code_id INT UNSIGNED DEFAULT NULL');

        $this->addSql('ALTER TABLE order_order
            ADD CONSTRAINT FK_order_promo_code
            FOREIGN KEY (promo_code_id) REFERENCES order_promo_code (id) ON DELETE SET NULL');

        $this->addSql('CREATE INDEX IDX_order_order_promo_code ON order_order (promo_code_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE order_order DROP FOREIGN KEY FK_order_promo_code');
        $this->addSql('DROP INDEX IDX_order_order_promo_code ON order_order');
        $this->addSql('ALTER TABLE order_order DROP COLUMN promo_code_id');
        $this->addSql('DROP TABLE order_promo_code');
    }
}