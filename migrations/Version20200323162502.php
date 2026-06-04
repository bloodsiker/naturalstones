<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20200323162502
 */
final class Version20200323162502 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return "BooksSwap (CommentBundle)";
    }

    public function up(Schema $schema) : void
    {
        $swap = $schema->createTable('books_swap');
        $swap->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $swap->addColumn('user_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $swap->addColumn('user_name', 'string', array('length' => 255, 'notnull' => false));
        $swap->addColumn('user_email', 'string', array('length' => 255, 'notnull' => false));
        $swap->addColumn('content', 'text', array('length' => 65535, 'notnull' => true));
        $swap->addColumn('is_active', 'boolean', array('notnull' => true));
        $swap->addColumn('created_at', 'datetime', array('notnull' => true));
        $swap->setPrimaryKey(['id']);
        $swap->addIndex(['user_id']);
        $swap->addForeignKeyConstraint($schema->getTable('user_users'), ['user_id'], ['id'], ['onDelete' => 'set null']);
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('books_swap');
    }
}
