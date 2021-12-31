<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20211231163851
 */
final class Version20211231163851 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Set products (ProductBundle)';
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        $productHasProducts = $schema->createTable('product_product_has_product');
        $productHasProducts->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $productHasProducts->addColumn('product_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $productHasProducts->addColumn('product_set_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $productHasProducts->addColumn('quantity', 'smallint', array('length' => 6, 'notnull' => true, 'default' => 1));
        $productHasProducts->addColumn('order_num', 'integer', array('length' => 11, 'notnull' => true, 'default' => 1));
        $productHasProducts->setPrimaryKey(['id']);
        $productHasProducts->addIndex(['product_id', 'product_set_id']);
        $productHasProducts->addForeignKeyConstraint($schema->getTable('product_product'), ['product_id'], ['id'], ['onDelete' => 'restrict']);
        $productHasProducts->addForeignKeyConstraint($schema->getTable('product_product'), ['product_set_id'], ['id'], ['onDelete' => 'restrict']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('product_product_has_product');
    }
}
