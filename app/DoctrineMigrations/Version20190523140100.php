<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20190523140100
 */
final class Version20190523140100 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return 'MainImage (MainImageBundle)';
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        $mainImage = $schema->createTable('main_image');
        $mainImage->addColumn('id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
        $mainImage->addColumn('title', 'string', ['length' => 255, 'notnull' => true]);
        $mainImage->addColumn('description', 'text', ['length' => 65535, 'notnull' => false]);
        $mainImage->addColumn('image_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $mainImage->addColumn('is_active', 'boolean', ['notnull' => true]);
        $mainImage->addColumn('order_num', 'integer', ['unsigned' => true, 'notnull' => true, 'default' => 0]);
        $mainImage->addColumn('created_at', 'datetime', ['notnull' => true]);
        $mainImage->setPrimaryKey(['id']);
        $mainImage->addForeignKeyConstraint($schema->getTable('media_image'), ['image_id'], ['id']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('main_image');
    }
}
