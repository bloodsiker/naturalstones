<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211003104307 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return "Product (ProductBundle)";
    }

    public function up(Schema $schema) : void
    {
        $zodiac = $schema->createTable('share_zodiacs');
        $zodiac->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $zodiac->addColumn('image_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $zodiac->addColumn('name', 'string', array('length' => 255, 'notnull' => true));
        $zodiac->addColumn('slug', 'string', array('length' => 100, 'notnull' => true));
        $zodiac->addColumn('is_active', 'boolean', array('notnull' => true));
        $zodiac->addColumn('is_show_main', 'boolean', array('notnull' => true));
        $zodiac->addColumn('created_at', 'datetime', array('notnull' => true));
        $zodiac->setPrimaryKey(array('id'));
        $zodiac->addIndex(['image_id']);
        $zodiac->addUniqueIndex(array('slug'));
        $zodiac->addForeignKeyConstraint($schema->getTable('media_image'), ['image_id'], ['id'], ['onDelete' => 'set null']);

        $stone = $schema->createTable('share_stones');
        $stone->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $stone->addColumn('image_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $stone->addColumn('name', 'string', array('length' => 255, 'notnull' => true));
        $stone->addColumn('description', 'text', array('length' => 65535, 'notnull' => false));
        $stone->addColumn('slug', 'string', array('length' => 100, 'notnull' => true));
        $stone->addColumn('is_active', 'boolean', array('notnull' => true));
        $stone->addColumn('is_show_main', 'boolean', array('notnull' => true));
        $stone->addColumn('created_at', 'datetime', array('notnull' => true));
        $stone->setPrimaryKey(array('id'));
        $stone->addIndex(['image_id']);
        $stone->addUniqueIndex(array('slug'));
        $stone->addForeignKeyConstraint($schema->getTable('media_image'), ['image_id'], ['id'], ['onDelete' => 'set null']);

        $stoneZodiac = $schema->createTable('share_stone_zodiacs');
        $stoneZodiac->addColumn('stone_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $stoneZodiac->addColumn('zodiac_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $stoneZodiac->addIndex(['stone_id', 'zodiac_id']);
        $stoneZodiac->addForeignKeyConstraint($schema->getTable('share_stones'), ['stone_id'], ['id'], ['onDelete' => 'cascade']);
        $stoneZodiac->addForeignKeyConstraint($zodiac, ['zodiac_id'], ['id'], ['onDelete' => 'cascade']);

        $size = $schema->createTable('share_sizes');
        $size->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $size->addColumn('name', 'string', array('length' => 100, 'notnull' => true));
        $size->addColumn('type', 'string', array('length' => 20, 'notnull' => true));
        $size->addColumn('is_active', 'boolean', array('notnull' => true));
        $size->setPrimaryKey(array('id'));

        $colour = $schema->createTable('share_colours');
        $colour->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $colour->addColumn('name', 'string', array('length' => 100, 'notnull' => true));
        $colour->addColumn('colour', 'string', array('length' => 100, 'notnull' => true));
        $colour->addColumn('slug', 'string', array('length' => 100, 'notnull' => true));
        $colour->addColumn('is_active', 'boolean', array('notnull' => true));
        $colour->setPrimaryKey(array('id'));
        $colour->addUniqueIndex(array('slug'));

        $category = $schema->createTable('product_category');
        $category->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $category->addColumn('name', 'string', array('length' => 255, 'notnull' => false));
        $category->addColumn('slug', 'string', array('length' => 255, 'notnull' => false));
        $category->addColumn('is_active', 'boolean', array('notnull' => true));
        $category->setPrimaryKey(['id']);

        $product = $schema->createTable('product_product');
        $product->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $product->addColumn('image_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $product->addColumn('category_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $product->addColumn('size_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $product->addColumn('name', 'string', array('length' => 255, 'notnull' => false));
        $product->addColumn('slug', 'string', array('length' => 255, 'notnull' => false));
        $product->addColumn('instagram_link', 'string', array('length' => 255, 'notnull' => false));
        $product->addColumn('description', 'text', array('length' => 65535, 'notnull' => false));
        $product->addColumn('price', 'float', array('notnull' => false, 'default' => 0));
        $product->addColumn('discount', 'float', array('notnull' => false));
        $product->addColumn('views', 'integer', array('unsigned' => true, 'notnull' => false, 'default' => 0));
        $product->addColumn('product_group', 'integer', array('unsigned' => true, 'notnull' => false, 'default' => 0));
        $product->addColumn('is_active', 'boolean', array('notnull' => true));
        $product->addColumn('is_available', 'boolean', array('notnull' => true));
        $product->addColumn('is_man', 'boolean', array('notnull' => true));
        $product->addColumn('is_woman', 'boolean', array('notnull' => true));
        $product->addColumn('is_main_product', 'boolean', array('notnull' => true));
        $product->addColumn('created_at', 'datetime', array('notnull' => true));
        $product->addColumn('updated_at', 'datetime', array('notnull' => false));
        $product->setPrimaryKey(['id']);
        $product->addIndex(['image_id', 'category_id', 'size_id']);
        $product->addForeignKeyConstraint($category, ['category_id'], ['id'], ['onDelete' => 'set null']);
        $product->addForeignKeyConstraint($schema->getTable('media_image'), ['image_id'], ['id'], ['onDelete' => 'set null']);
        $product->addForeignKeyConstraint($size, ['size_id'], ['id'], ['onDelete' => 'set null']);

        $productImages = $schema->createTable('product_product_has_image');
        $productImages->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $productImages->addColumn('product_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $productImages->addColumn('image_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $productImages->addColumn('order_num', 'integer', array('length' => 11, 'notnull' => true, 'default' => 1));
        $productImages->setPrimaryKey(['id']);
        $productImages->addIndex(['product_id', 'image_id']);
        $productImages->addForeignKeyConstraint($product, ['product_id'], ['id'], ['onDelete' => 'restrict']);
        $productImages->addForeignKeyConstraint($schema->getTable('media_image'), ['image_id'], ['id'], ['onDelete' => 'restrict']);

        $productStone = $schema->createTable('product_product_stones');
        $productStone->addColumn('product_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $productStone->addColumn('stone_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $productStone->addIndex(['product_id', 'stone_id']);
        $productStone->addForeignKeyConstraint($product, ['product_id'], ['id'], ['onDelete' => 'cascade']);
        $productStone->addForeignKeyConstraint($schema->getTable('share_stones'), ['stone_id'], ['id'], ['onDelete' => 'cascade']);

//        $productSize = $schema->createTable('product_product_sizes');
//        $productSize->addColumn('product_id', 'integer', array('unsigned' => true, 'notnull' => true));
//        $productSize->addColumn('size_id', 'integer', array('unsigned' => true, 'notnull' => true));
//        $productSize->addIndex(['product_id', 'size_id']);
//        $productSize->addForeignKeyConstraint($product, ['product_id'], ['id'], ['onDelete' => 'cascade']);
//        $productSize->addForeignKeyConstraint($schema->getTable('share_sizes'), ['size_id'], ['id'], ['onDelete' => 'cascade']);

        $productTags = $schema->createTable('product_product_colours');
        $productTags->addColumn('product_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $productTags->addColumn('colour_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $productTags->addIndex(['product_id', 'colour_id']);
        $productTags->addForeignKeyConstraint($product, ['product_id'], ['id'], ['onDelete' => 'cascade']);
        $productTags->addForeignKeyConstraint($schema->getTable('share_colours'), ['colour_id'], ['id'], ['onDelete' => 'cascade']);

        $productTags = $schema->createTable('product_product_tags');
        $productTags->addColumn('product_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $productTags->addColumn('tag_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $productTags->addIndex(['product_id', 'tag_id']);
        $productTags->addForeignKeyConstraint($product, ['product_id'], ['id'], ['onDelete' => 'cascade']);
        $productTags->addForeignKeyConstraint($schema->getTable('share_tags'), ['tag_id'], ['id'], ['onDelete' => 'cascade']);

        $productView = $schema->createTable('product_product_info_view');
        $productView->addColumn('id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
        $productView->addColumn('product_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $productView->addColumn('view_at', 'date', ['notnull' => true]);
        $productView->addColumn('views', 'smallint', ['length' => 6, 'notnull' => true, 'default' => 0]);
        $productView->setPrimaryKey(['id']);
        $productView->addIndex(['product_id']);
        $productView->addForeignKeyConstraint($schema->getTable('product_product'), ['product_id'], ['id'], ['onDelete' => 'set null']);
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('product_product_info_view');
        $schema->dropTable('product_product_stones');
        $schema->dropTable('product_product_tags');
        $schema->dropTable('product_product_sizes');
        $schema->dropTable('product_product_colours');
        $schema->dropTable('product_product_has_image');
        $schema->dropTable('product_product');
        $schema->dropTable('product_category');
        $schema->dropTable('share_sizes');
        $schema->dropTable('share_colours');
        $schema->dropTable('share_stones');
    }
}
