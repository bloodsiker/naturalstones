<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260623000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add spin_promo_code_id to order_order for wheel of fortune promo code prizes';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE order_order ADD spin_promo_code_id INT UNSIGNED DEFAULT NULL');

        $this->addSql('ALTER TABLE order_order
            ADD CONSTRAINT FK_order_spin_promo_code
            FOREIGN KEY (spin_promo_code_id) REFERENCES order_promo_code (id) ON DELETE SET NULL');

        $this->addSql('CREATE INDEX IDX_order_order_spin_promo_code ON order_order (spin_promo_code_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE order_order DROP FOREIGN KEY FK_order_spin_promo_code');
        $this->addSql('DROP INDEX IDX_order_order_spin_promo_code ON order_order');
        $this->addSql('ALTER TABLE order_order DROP COLUMN spin_promo_code_id');
    }
}