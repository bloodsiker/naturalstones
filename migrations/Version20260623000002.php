<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260623000002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add promo_code_value to wheel_spin_option for wheel-of-fortune promo code prizes';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wheel_spin_option ADD promo_code_value VARCHAR(20) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wheel_spin_option DROP COLUMN promo_code_value');
    }
}