<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202207262030277 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Information (InformationBundle)';
    }

    public function up(Schema $schema) : void
    {
        $information = $schema->createTable('informations');
        $information->addColumn('id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
        $information->addColumn('is_active', 'boolean', ['notnull' => true]);
        $information->addColumn('started_at', 'datetime', ['notnull' => false]);
        $information->addColumn('finished_at', 'datetime', ['notnull' => false]);
        $information->addColumn('created_at', 'datetime', ['notnull' => true]);
        $information->setPrimaryKey(['id']);

        $informationTranslation = $schema->createTable('informations_translation');
        $informationTranslation->addColumn('id', 'integer', ['notnull' => true, 'autoincrement' => true]);
        $informationTranslation->addColumn('translatable_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $informationTranslation->addColumn('title', 'string', ['length' => 255, 'notnull' => false]);
        $informationTranslation->addColumn('url', 'string', ['length' => 255, 'notnull' => false]);
        $informationTranslation->addColumn('description', 'text', ['length' => 65535, 'notnull' => false]);
        $informationTranslation->addColumn('locale', 'string', ['length' => 2, 'notnull' => true]);
        $informationTranslation->setPrimaryKey(['id']);
        $informationTranslation->addUniqueIndex(['translatable_id', 'locale']);
        $informationTranslation->addForeignKeyConstraint($schema->getTable('informations'), ['translatable_id'], ['id'], ['onDelete' => 'cascade']);
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('informations_translation');
        $schema->dropTable('informations');
    }
}
