<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202202011019307 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return "Product option (ProductBundle)";
    }

    public function up(Schema $schema) : void
    {
        $productHasOption = $schema->createTable('product_product_has_option');
        $productHasOption->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $productHasOption->addColumn('product_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $productHasOption->addColumn('value', 'string',  array('length' => 255, 'notnull' => true));
        $productHasOption->addColumn('image_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $productHasOption->addColumn('price', 'float', array('notnull' => false, 'default' => 0));
        $productHasOption->addColumn('order_num', 'integer', array('length' => 11, 'notnull' => true, 'default' => 1));
        $productHasOption->setPrimaryKey(['id']);
        $productHasOption->addIndex(['product_id', 'image_id']);
        $productHasOption->addForeignKeyConstraint($schema->getTable('product_product'), ['product_id'], ['id'], ['onDelete' => 'cascade']);
        $productHasOption->addForeignKeyConstraint($schema->getTable('media_image'), ['image_id'], ['id'], ['onDelete' => 'set null']);

        $product = $schema->getTable('product_product');
        $product->addColumn('option_type', 'integer', ['columnDefinition' => 'TINYINT(2) UNSIGNED DEFAULT 1 NOT NULL']);
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('product_product_has_option');
        $product = $schema->getTable('product_product');
        $product->dropColumn('option_type');
    }
}
