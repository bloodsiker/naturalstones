<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202208092030278 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return 'ProductOptionLabel (ProductBundle)';
    }

    public function up(Schema $schema) : void
    {
        $labelTranslation = $schema->createTable('product_option_label_translation');
        $labelTranslation->addColumn('id', 'integer', ['notnull' => true, 'autoincrement' => true]);
        $labelTranslation->addColumn('translatable_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $labelTranslation->addColumn('name', 'string', ['length' => 100, 'notnull' => false]);
        $labelTranslation->addColumn('locale', 'string', ['length' => 2, 'notnull' => true]);
        $labelTranslation->setPrimaryKey(['id']);
        $labelTranslation->addUniqueIndex(['translatable_id', 'locale']);
        $labelTranslation->addForeignKeyConstraint($schema->getTable('product_option_label'), ['translatable_id'], ['id'], ['onDelete' => 'cascade']);

        $label = $schema->getTable('product_option_label');
        $label->dropColumn('name');
    }

    public function down(Schema $schema) : void
    {

    }
}
