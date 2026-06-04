<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202405060643555 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $productComment = $schema->createTable('product_comments');
        $productComment->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $productComment->addColumn('product_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $productComment->addColumn('user_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $productComment->addColumn('user_name', 'string', array('length' => 255, 'notnull' => false));
        $productComment->addColumn('user_email', 'string', array('length' => 255, 'notnull' => false));
        $productComment->addColumn('comment', 'text', array('length' => 65535, 'notnull' => true));
        $productComment->addColumn('rating', 'smallint', array('length' => 6, 'notnull' => true));
        $productComment->addColumn('is_active', 'boolean', array('notnull' => true));
        $productComment->addColumn('created_at', 'datetime', array('notnull' => true));
        $productComment->setPrimaryKey(['id']);
        $productComment->addIndex(['product_id', 'user_id']);
        $productComment->addForeignKeyConstraint($schema->getTable('product_product'), ['product_id'], ['id'], ['onDelete' => 'cascade']);
        $productComment->addForeignKeyConstraint($schema->getTable('user_users'), ['user_id'], ['id'], ['onDelete' => 'set null']);

        // bookVotesResult
        $productCommentVotes = $schema->createTable('product_comment_vote_results');
        $productCommentVotes->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $productCommentVotes->addColumn('comment_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $productCommentVotes->addColumn('ip', 'integer', array('unsigned' => true, 'notnull' => false));
        $productCommentVotes->addColumn('result_vote', 'boolean', array('notnull' => true));
        $productCommentVotes->addColumn('voted_at', 'datetime', array('notnull' => true));
        $productCommentVotes->setPrimaryKey(['id']);
        $productCommentVotes->addIndex(['comment_id']);
        $productCommentVotes->addForeignKeyConstraint($productComment, ['comment_id'], ['id'], ['onDelete' => 'cascade']);
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $schema->dropTable('product_comment_vote_results');
        $schema->dropTable('product_product');
    }
}
