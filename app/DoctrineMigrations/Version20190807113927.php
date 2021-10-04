<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20190807113927
 */
final class Version20190807113927 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return "Book view by date (BookBundle)";
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema) : void
    {
        $infoView = $schema->createTable('books_info_view');
        $infoView->addColumn('id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
        $infoView->addColumn('book_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $infoView->addColumn('view_at', 'date', ['notnull' => true]);
        $infoView->addColumn('views', 'smallint', ['length' => 6, 'notnull' => true, 'default' => 0]);
        $infoView->addColumn('downloads', 'smallint', ['length' => 6, 'notnull' => true, 'default' => 0]);
        $infoView->setPrimaryKey(['id']);
        $infoView->addForeignKeyConstraint($schema->getTable('books'), ['book_id'], ['id'], ['onDelete' => 'set null']);

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $schema->dropTable('books_info_view');
    }
}
