<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20190325083156
 */
final class Version20190325083156 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Get info download book (BookBundle)';
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        $infoDownload = $schema->createTable('books_info_download');
        $infoDownload->addColumn('id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
        $infoDownload->addColumn('book_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $infoDownload->addColumn('download_at', 'datetime', ['notnull' => true]);
        $infoDownload->addColumn('ip', 'string', ['length' => 20, 'notnull' => true]);
        $infoDownload->setPrimaryKey(['id']);
        $infoDownload->addForeignKeyConstraint($schema->getTable('books'), ['book_id'], ['id'], ['onDelete' => 'set null']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('books_info_download');
    }
}
