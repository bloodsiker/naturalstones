<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20190516131607
 */
final class Version20190516131607 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return "BookCollection (BookBundle)";
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema) : void
    {
        // book
        $book = $schema->createTable('books_collection');
        $book->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $book->addColumn('image_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $book->addColumn('title', 'string', array('length' => 255, 'notnull' => true));
        $book->addColumn('slug', 'string', array('length' => 100, 'notnull' => true));
        $book->addColumn('description', 'text', array('length' => 65535, 'notnull' => true));
        $book->addColumn('is_active', 'boolean', array('notnull' => true));
        $book->addColumn('views', 'integer', array('unsigned' => true, 'notnull' => false, 'default' => 0));
        $book->addColumn('created_at', 'datetime', array('notnull' => true));
        $book->addColumn('updated_at', 'datetime', array('notnull' => false));
        $book->setPrimaryKey(['id']);
        $book->addIndex(['image_id']);
        $book->addForeignKeyConstraint($schema->getTable('media_image'), ['image_id'], ['id'], ['onDelete' => 'set null']);

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $schema->dropTable('books_collection');
    }
}
