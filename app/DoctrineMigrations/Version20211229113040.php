<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211229113040 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return "Add product metals (ShareBundle)";
    }

    public function up(Schema $schema) : void
    {
        $metal = $schema->createTable('share_metals');
        $metal->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $metal->addColumn('name', 'string', array('length' => 255, 'notnull' => true));
        $metal->addColumn('slug', 'string', array('length' => 100, 'notnull' => true));
        $metal->addColumn('is_active', 'boolean', array('notnull' => true));
        $metal->setPrimaryKey(array('id'));
        $metal->addUniqueIndex(array('slug'));

        $productMetal = $schema->createTable('product_product_metals');
        $productMetal->addColumn('product_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $productMetal->addColumn('metal_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $productMetal->addIndex(['product_id', 'metal_id']);
        $productMetal->addForeignKeyConstraint($schema->getTable('product_product'), ['product_id'], ['id'], ['onDelete' => 'cascade']);
        $productMetal->addForeignKeyConstraint($metal, ['metal_id'], ['id'], ['onDelete' => 'cascade']);
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('product_product_metals');
        $schema->dropTable('share_metal');
    }
}
