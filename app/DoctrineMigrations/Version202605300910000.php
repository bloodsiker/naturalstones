<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version202605300910000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add user relation to orders';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE order_order ADD user_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE order_order ADD CONSTRAINT FK_7D7B6AF2A76ED395 FOREIGN KEY (user_id) REFERENCES user_users (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_7D7B6AF2A76ED395 ON order_order (user_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE order_order DROP FOREIGN KEY FK_7D7B6AF2A76ED395');
        $this->addSql('DROP INDEX IDX_7D7B6AF2A76ED395 ON order_order');
        $this->addSql('ALTER TABLE order_order DROP user_id');
    }
}
