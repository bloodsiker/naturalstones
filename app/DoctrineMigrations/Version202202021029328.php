<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202202021029328 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $productLabel = $schema->createTable('product_option_label');
        $productLabel->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $productLabel->addColumn('name', 'string', array('length' => 255, 'notnull' => true));
        $productLabel->addColumn('is_active', 'boolean', array('notnull' => true));
        $productLabel->setPrimaryKey(array('id'));

        $product = $schema->getTable('product_product');
        $product->addColumn('option_label_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $product->addIndex(['option_label_id']);
        $product->addForeignKeyConstraint($productLabel, ['option_label_id'], ['id'], ['onDelete' => 'set null']);
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('product_option_label');
    }
}
