<?php

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version202606011030000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Generate missing success-page tokens for legacy orders';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE order_order SET secret = SHA1(CONCAT('legacy-order-', id, '-', created_at)) WHERE secret IS NULL OR secret = ''");
    }

    public function down(Schema $schema): void
    {
    }
}
