<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20190221083923
 */
final class Version20190221083923 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Page redirect (PageBundle)';
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        $redirect = $schema->createTable('page_page_redirect');
        $redirect->addColumn('id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
        $redirect->addColumn('from_path', 'string', ['length' => 255, 'notnull' => true]);
        $redirect->addColumn('to_path', 'string', ['length' => 255, 'notnull' => false]);
        $redirect->addColumn('to_page_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $redirect->addColumn('is_active', 'boolean', ['notnull' => true, 'default' => true]);
        $redirect->addColumn('type', 'integer', ['columnDefinition' => 'TINYINT(1) UNSIGNED DEFAULT 1 NOT NULL']);
        $redirect->addColumn('help', 'string', ['length' => 255, 'notnull' => false]);
        $redirect->setPrimaryKey(['id']);
        $redirect->addUniqueIndex(['from_path'], 'idx_unique_from_path');
        $redirect->addForeignKeyConstraint($schema->getTable('page_page'), ['to_page_id'], ['id'], ['onDelete' => 'cascade']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('page_page_redirect');
    }
}
