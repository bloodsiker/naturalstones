<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20181213145836
 */
final class Version20181213145836 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return "OrderBoard (OrderBundle))";
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema) : void
    {
        // orderBoards
        $orderBoard = $schema->createTable('order_board');
        $orderBoard->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $orderBoard->addColumn('book_title', 'string', array('length' => 255, 'notnull' => true));
        $orderBoard->addColumn('user_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $orderBoard->addColumn('user_name', 'string', array('length' => 255, 'notnull' => false));
        $orderBoard->addColumn('status', 'smallint', array('length' => 1, 'notnull' => true));
        $orderBoard->addColumn('vote', 'smallint', array('length' => 6, 'notnull' => true, 'default' => 0));
        $orderBoard->addColumn('created_at', 'datetime', array('notnull' => true));
        $orderBoard->setPrimaryKey(['id']);
        $orderBoard->addIndex(['user_id']);
        $orderBoard->addForeignKeyConstraint($schema->getTable('user_users'), ['user_id'], ['id'], ['onDelete' => 'set null']);

        $orderVote = $schema->createTable('order_board_votes_result');
        $orderVote->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $orderVote->addColumn('order_board_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $orderVote->addColumn('ip', 'integer', array('unsigned' => true, 'notnull' => false));
        $orderVote->addColumn('voted_at', 'datetime', array('notnull' => true));
        $orderVote->setPrimaryKey(['id']);
        $orderVote->addIndex(['order_board_id']);
        $orderVote->addForeignKeyConstraint($orderBoard, ['order_board_id'], ['id'], ['onDelete' => 'cascade']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $schema->dropTable('order_board_votes_result');
        $schema->dropTable('order_board');
    }
}
