<?php

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version202606010930000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE order_order ADD telegram_chat_id VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE order_order ADD telegram_message_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE order_order DROP telegram_chat_id');
        $this->addSql('ALTER TABLE order_order DROP telegram_message_id');
    }
}
