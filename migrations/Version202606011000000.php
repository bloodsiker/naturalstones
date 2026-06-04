<?php

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version202606011000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Increase order monetary fields capacity above 9999.99';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE order_order CHANGE total_sum total_sum NUMERIC(12, 2) DEFAULT 0.00 NOT NULL, CHANGE discount_promo discount_promo NUMERIC(12, 2) DEFAULT 0.00 NOT NULL, CHANGE order_sum order_sum NUMERIC(12, 2) DEFAULT 0.00 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE order_order CHANGE total_sum total_sum NUMERIC(6, 2) DEFAULT 0.00 NOT NULL, CHANGE discount_promo discount_promo NUMERIC(6, 2) DEFAULT 0.00 NOT NULL, CHANGE order_sum order_sum NUMERIC(6, 2) DEFAULT 0.00 NOT NULL');
    }
}
