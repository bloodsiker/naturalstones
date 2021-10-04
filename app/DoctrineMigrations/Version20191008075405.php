<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191008075405 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return "Add BookCollectionHasBook, BookCollectionGenres (BookBundle))";
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema) : void
    {
        // bookCollectionHasBook
        $bookCollectionHasBook = $schema->createTable('books_collection_has_book');
        $bookCollectionHasBook->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $bookCollectionHasBook->addColumn('book_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $bookCollectionHasBook->addColumn('collection_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $bookCollectionHasBook->addColumn('order_num', 'integer', array('length' => 11, 'notnull' => true, 'default' => 1));
        $bookCollectionHasBook->setPrimaryKey(['id']);
        $bookCollectionHasBook->addIndex(['book_id', 'collection_id']);
        $bookCollectionHasBook->addForeignKeyConstraint($schema->getTable('books'), ['book_id'], ['id'], ['onDelete' => 'restrict']);
        $bookCollectionHasBook->addForeignKeyConstraint($schema->getTable('books_collection'), ['collection_id'], ['id'], ['onDelete' => 'restrict']);

        // bookCollectionGenres
        $bookCollectionGenres = $schema->createTable('books_collection_genres');
        $bookCollectionGenres->addColumn('collection_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $bookCollectionGenres->addColumn('genre_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $bookCollectionGenres->addIndex(['collection_id', 'genre_id']);
        $bookCollectionGenres->addForeignKeyConstraint($schema->getTable('books_collection'), ['collection_id'], ['id'], ['onDelete' => 'cascade']);
        $bookCollectionGenres->addForeignKeyConstraint($schema->getTable('genres'), ['genre_id'], ['id'], ['onDelete' => 'cascade']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $schema->dropTable('books_collection_has_book');
        $schema->dropTable('books_collection_genres');
    }
}
