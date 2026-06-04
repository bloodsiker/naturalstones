<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20181014125608
 */
final class Version20181014125608 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return "Books, BookHasFile, BookHasTags, BookHasRelated, BookComments, BookVotesResult (BookBundle)";
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema) : void
    {
        // book
        $book = $schema->createTable('books');
        $book->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $book->addColumn('image_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $book->addColumn('name', 'string', array('length' => 255, 'notnull' => true));
        $book->addColumn('slug', 'string', array('length' => 100, 'notnull' => true));
        $book->addColumn('description', 'text', array('length' => 65535, 'notnull' => true));
        $book->addColumn('year', 'integer', array('unsigned' => true, 'notnull' => false, 'length' => 4));
        $book->addColumn('restrict_age', 'integer', array('unsigned' => true, 'notnull' => false, 'length' => 3));
        $book->addColumn('pages', 'integer', array('unsigned' => true, 'notnull' => false, 'length' => 4));
        $book->addColumn('ratePlus', 'integer', array('unsigned' => true, 'notnull' => false, 'length' => 7));
        $book->addColumn('rateMinus', 'integer', array('unsigned' => true, 'notnull' => false, 'length' => 7));
        $book->addColumn('is_active', 'boolean', array('notnull' => true));
        $book->addColumn('is_allow_download', 'boolean', array('notnull' => true));
        $book->addColumn('download', 'integer', array('unsigned' => true, 'notnull' => false, 'default' => 0));
        $book->addColumn('views', 'integer', array('unsigned' => true, 'notnull' => false, 'default' => 0));
        $book->addColumn('created_at', 'datetime', array('notnull' => true));
        $book->addColumn('updated_at', 'datetime', array('notnull' => false));
        $book->setPrimaryKey(['id']);
        $book->addIndex(['image_id']);
        $book->addForeignKeyConstraint($schema->getTable('media_image'), ['image_id'], ['id'], ['onDelete' => 'set null']);

        // bookHasFile
        $bookHasFile = $schema->createTable('books_has_file');
        $bookHasFile->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $bookHasFile->addColumn('book_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $bookHasFile->addColumn('book_file_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $bookHasFile->addColumn('order_num', 'integer', array('length' => 11, 'notnull' => true, 'default' => 1));
        $bookHasFile->setPrimaryKey(['id']);
        $bookHasFile->addIndex(['book_id', 'book_file_id']);
        $bookHasFile->addForeignKeyConstraint($book, ['book_id'], ['id'], ['onDelete' => 'restrict']);
        $bookHasFile->addForeignKeyConstraint($schema->getTable('media_file'), ['book_file_id'], ['id'], ['onDelete' => 'restrict']);

        // bookHasRelated
        $bookHasRelated = $schema->createTable('books_has_related');
        $bookHasRelated->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $bookHasRelated->addColumn('book_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $bookHasRelated->addColumn('related_book_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $bookHasRelated->addColumn('order_num', 'integer', array('length' => 11, 'notnull' => true, 'default' => 1));
        $bookHasRelated->setPrimaryKey(['id']);
        $bookHasRelated->addIndex(['book_id', 'related_book_id']);
        $bookHasRelated->addForeignKeyConstraint($book, ['book_id'], ['id'], ['onDelete' => 'restrict']);
        $bookHasRelated->addForeignKeyConstraint($book, ['related_book_id'], ['id'], ['onDelete' => 'restrict']);

        // bookTags
        $bookTags = $schema->createTable('books_tags');
        $bookTags->addColumn('book_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $bookTags->addColumn('tag_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $bookTags->addIndex(['book_id', 'tag_id']);
        $bookTags->addForeignKeyConstraint($book, ['book_id'], ['id'], ['onDelete' => 'cascade']);
        $bookTags->addForeignKeyConstraint($schema->getTable('share_tags'), ['tag_id'], ['id'], ['onDelete' => 'cascade']);

        // bookComments
        $bookComment = $schema->createTable('books_comments');
        $bookComment->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $bookComment->addColumn('book_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $bookComment->addColumn('user_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $bookComment->addColumn('user_name', 'string', array('length' => 255, 'notnull' => false));
        $bookComment->addColumn('user_email', 'string', array('length' => 255, 'notnull' => false));
        $bookComment->addColumn('comment', 'text', array('length' => 65535, 'notnull' => true));
        $bookComment->addColumn('rating', 'smallint', array('length' => 6, 'notnull' => true));
        $bookComment->addColumn('is_active', 'boolean', array('notnull' => true));
        $bookComment->addColumn('created_at', 'datetime', array('notnull' => true));
        $bookComment->setPrimaryKey(['id']);
        $bookComment->addIndex(['book_id', 'user_id']);
        $bookComment->addForeignKeyConstraint($book, ['book_id'], ['id'], ['onDelete' => 'cascade']);
        $bookComment->addForeignKeyConstraint($schema->getTable('user_users'), ['user_id'], ['id'], ['onDelete' => 'set null']);

        // bookVotesResult
        $bookCommentVotes = $schema->createTable('books_comments_votes_result');
        $bookCommentVotes->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $bookCommentVotes->addColumn('comment_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $bookCommentVotes->addColumn('ip', 'integer', array('unsigned' => true, 'notnull' => false));
        $bookCommentVotes->addColumn('result_vote', 'boolean', array('notnull' => true));
        $bookCommentVotes->addColumn('voted_at', 'datetime', array('notnull' => true));
        $bookCommentVotes->setPrimaryKey(['id']);
        $bookCommentVotes->addIndex(['comment_id']);
        $bookCommentVotes->addForeignKeyConstraint($bookComment, ['comment_id'], ['id'], ['onDelete' => 'cascade']);

        // bookVotesResult
        $bookVotes = $schema->createTable('books_votes_result');
        $bookVotes->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $bookVotes->addColumn('book_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $bookVotes->addColumn('ip', 'integer', array('unsigned' => true, 'notnull' => false));
        $bookVotes->addColumn('result_vote', 'boolean', array('notnull' => true));
        $bookVotes->addColumn('voted_at', 'datetime', array('notnull' => true));
        $bookVotes->setPrimaryKey(['id']);
        $bookVotes->addIndex(['book_id']);
        $bookVotes->addForeignKeyConstraint($book, ['book_id'], ['id'], ['onDelete' => 'cascade']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $schema->dropTable('books_votes_result');
        $schema->dropTable('books_has_related');
        $schema->dropTable('books_has_file');
        $schema->dropTable('books_tags');
        $schema->dropTable('books_comments');
        $schema->dropTable('books');
    }
}
