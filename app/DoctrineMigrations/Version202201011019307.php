<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202201011019307 extends AbstractMigration
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
        $productHasMetals = $schema->createTable('product_product_has_option_metal');
        $productHasMetals->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $productHasMetals->addColumn('product_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $productHasMetals->addColumn('metal_id', 'integer', ['unsigned' => true, 'notnull' => true]);
        $productHasMetals->addColumn('image_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $productHasMetals->addColumn('price', 'float', array('notnull' => false, 'default' => 0));
        $productHasMetals->addColumn('order_num', 'integer', array('length' => 11, 'notnull' => true, 'default' => 1));
        $productHasMetals->setPrimaryKey(['id']);
        $productHasMetals->addIndex(['product_id', 'image_id', 'metal_id']);
        $productHasMetals->addForeignKeyConstraint($schema->getTable('product_product'), ['product_id'], ['id'], ['onDelete' => 'restrict']);
        $productHasMetals->addForeignKeyConstraint($schema->getTable('share_metals'), ['metal_id'], ['id'], ['onDelete' => 'restrict']);
        $productHasMetals->addForeignKeyConstraint($schema->getTable('media_image'), ['image_id'], ['id'], ['onDelete' => 'set null']);


        $productHasColours = $schema->createTable('product_product_has_option_colour');
        $productHasColours->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $productHasColours->addColumn('product_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $productHasColours->addColumn('colour_id', 'integer', ['unsigned' => true, 'notnull' => true]);
        $productHasColours->addColumn('image_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $productHasColours->addColumn('price', 'float', array('notnull' => false, 'default' => 0));
        $productHasColours->addColumn('order_num', 'integer', array('length' => 11, 'notnull' => true, 'default' => 1));
        $productHasColours->setPrimaryKey(['id']);
        $productHasColours->addIndex(['product_id', 'image_id', 'colour_id']);
        $productHasColours->addForeignKeyConstraint($schema->getTable('product_product'), ['product_id'], ['id'], ['onDelete' => 'restrict']);
        $productHasColours->addForeignKeyConstraint($schema->getTable('share_colours'), ['colour_id'], ['id'], ['onDelete' => 'restrict']);
        $productHasColours->addForeignKeyConstraint($schema->getTable('media_image'), ['image_id'], ['id'], ['onDelete' => 'set null']);
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('product_product_has_option_colour');
        $schema->dropTable('product_product_has_option_metal');
    }
}
