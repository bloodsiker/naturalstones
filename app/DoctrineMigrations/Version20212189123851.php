<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20212189123851
 */
final class Version20212189123851 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Search (ProductBundle)';
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        $history = $schema->createTable('product_search_history');
        $history->addColumn('id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
        $history->addColumn('search', 'string', ['length' => 255, 'notnull' => false]);
        $history->addColumn('ip', 'string', ['length' => 20, 'notnull' => true]);
        $history->addColumn('created_at', 'datetime', ['notnull' => true]);
        $history->setPrimaryKey(['id']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('product_search_history');
    }
}
