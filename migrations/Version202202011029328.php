<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202202011029328 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return "Category option (ProductBundle)";
    }

    public function up(Schema $schema) : void
    {
        $category = $schema->getTable('product_category');
        $category->addColumn('image_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $category->addColumn('description', 'text', array('length' => 65535, 'notnull' => false));
        $category->addColumn('order_num', 'integer', array('length' => 11, 'notnull' => true, 'default' => 1));
        $category->addIndex(['image_id']);
        $category->addForeignKeyConstraint($schema->getTable('media_image'), ['image_id'], ['id'], ['onDelete' => 'set null']);

        $product = $schema->getTable('product_product');
        $product->addColumn('order_num', 'integer', array('length' => 11, 'notnull' => true, 'default' => 1));
    }

    public function down(Schema $schema) : void
    {
        $category = $schema->getTable('product_category');
        $category->dropColumn('image_id');
        $category->dropColumn('order_num');
        $category->dropColumn('description');

        $product = $schema->getTable('product_product');
        $product->dropColumn('order_num');
    }
}
