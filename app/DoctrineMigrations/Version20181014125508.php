<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class 20181014125508
 */
final class Version20181014125508 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return "Image, File (MediaBundle))";
    }

    public function up(Schema $schema) : void
    {
        $book = $schema->createTable('media_image');
        $book->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $book->addColumn('description', 'string', array('length' => 255, 'notnull' => false));
        $book->addColumn('path', 'string', array('length' => 255, 'notnull' => true));
        $book->addColumn('mime_type', 'string', array('length' => 100, 'notnull' => true));
        $book->addColumn('size', 'integer', array('unsigned' => true, 'notnull' => false));
        $book->addColumn('width', 'integer', array('unsigned' => true, 'notnull' => false));
        $book->addColumn('height', 'integer', array('unsigned' => true, 'notnull' => false));
        $book->addColumn('is_active', 'boolean', array('notnull' => true));
        $book->addColumn('user_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $book->addColumn('created_at', 'datetime', array('notnull' => true));
        $book->addColumn('updated_at', 'datetime', array('notnull' => true));
        $book->setPrimaryKey(['id']);
        $book->addIndex(['user_id']);
        $book->addForeignKeyConstraint($schema->getTable('user_users'), ['user_id'], ['id'], ['onDelete' => 'set null']);

        $book = $schema->createTable('media_file');
        $book->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $book->addColumn('description', 'string', array('length' => 255, 'notnull' => false));
        $book->addColumn('path', 'string', array('length' => 255, 'notnull' => true));
        $book->addColumn('mime_type', 'string', array('length' => 100, 'notnull' => true));
        $book->addColumn('size', 'integer', array('unsigned' => true, 'notnull' => false));
        $book->addColumn('is_active', 'boolean', array('notnull' => true));
        $book->addColumn('user_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $book->addColumn('created_at', 'datetime', array('notnull' => true));
        $book->addColumn('updated_at', 'datetime', array('notnull' => true));
        $book->setPrimaryKey(['id']);
        $book->addIndex(['user_id']);
        $book->addForeignKeyConstraint($schema->getTable('user_users'), ['user_id'], ['id'], ['onDelete' => 'set null']);
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('media_file');
        $schema->dropTable('media_image');
    }
}
