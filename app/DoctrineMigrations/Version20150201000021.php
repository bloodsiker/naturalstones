<?php

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20150201000021
 */
final class Version20150201000021 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Site variables (PageBundle)';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $placement = $schema->createTable('page_site_variable_placement');
        $placement->addColumn('id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
        $placement->addColumn('alias', 'string', ['length' => 255, 'notnull' => true]);
        $placement->setPrimaryKey(['id']);
        $placement->addUniqueIndex(['alias']);

        $variable = $schema->createTable('page_site_variable');
        $variable->addColumn('id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
        $variable->addColumn('name', 'string', ['length' => 255, 'notnull' => true]);
        $variable->addColumn('value', 'text', ['length' => 65535, 'notnull' => false]);
        $variable->addColumn('placement_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $variable->addColumn('is_active', 'boolean', ['notnull' => true]);
        $variable->addColumn('created_at', 'datetime', ['notnull' => true]);
        $variable->addColumn('modified_at', 'datetime', ['notnull' => true]);
        $variable->setPrimaryKey(['id']);
        $variable->addForeignKeyConstraint($placement, ['placement_id'], ['id'], ['onDelete' => 'set null']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('page_site_variable');
        $schema->dropTable('page_site_variable_placement');
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function postUp(Schema $schema)
    {
        $this->connection->executeQuery('
            INSERT INTO `page_site_variable_placement` (`id`, `alias`)
            VALUES (null, \'head\'),
                   (null, \'body-begin\'),
                   (null, \'body-end\'),
                   (null, \'footer-counters\'),
                   (null, \'homepage-bottom\')
        ');
    }
}
