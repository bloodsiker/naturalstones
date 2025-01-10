<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202501101458188 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $productLog = $schema->createTable('product_product_log');
        $productLog->addColumn('id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
        $productLog->addColumn('product_id', 'integer', ['unsigned' => true, 'notnull' => true]);
        $productLog->addColumn('new_price', 'float', ['notnull' => false, 'default' => 0]);
        $productLog->addColumn('old_price', 'float', ['notnull' => false, 'default' => 0]);
        $productLog->addColumn('created_at', 'datetime', ['notnull' => true]);
        $productLog->setPrimaryKey(['id']);
        $productLog->addIndex(['product_id']);
        $productLog->addForeignKeyConstraint($schema->getTable('product_product'), ['product_id'], ['id'], ['onDelete' => 'restrict']);
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $schema->dropTable('product_product_log');
    }
}
