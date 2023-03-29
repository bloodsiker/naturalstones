<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20211289123851
 */
final class Version20211289123851 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Order (OrderBundle)';
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        $order = $schema->createTable('order_order');
        $order->addColumn('id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
        $order->addColumn('fio', 'string', ['length' => 100, 'notnull' => false]);
        $order->addColumn('email', 'string', ['length' => 100, 'notnull' => false]);
        $order->addColumn('phone', 'string', ['length' => 100, 'notnull' => false]);
        $order->addColumn('messenger', 'string', ['length' => 100, 'notnull' => false]);
        $order->addColumn('address', 'text', array('length' => 65535, 'notnull' => false));
        $order->addColumn('comment', 'text', array('length' => 65535, 'notnull' => false));
        $order->addColumn('total_sum', 'decimal', ['precision' => 6, 'scale' => 2, 'notnull' => true, 'default' => 0.00]);
        $order->addColumn('discount_promo', 'decimal', ['precision' => 6, 'scale' => 2, 'notnull' => true, 'default' => 0.00]);
        $order->addColumn('order_sum', 'decimal', ['precision' => 6, 'scale' => 2, 'notnull' => true, 'default' => 0.00]);
        $order->addColumn('status', 'smallint', ['unsigned' => true, 'notnull' => false, 'length' => 2]);
        $order->addColumn('type', 'smallint', ['unsigned' => true, 'notnull' => true, 'length' => 1]);
        $order->addColumn('call_me', 'boolean', ['notnull' => true, 'default' => false]);
        $order->addColumn('created_at', 'datetime', ['notnull' => true]);
        $order->setPrimaryKey(['id']);

        $orderHasItem = $schema->createTable('order_order_has_item');
        $orderHasItem->addColumn('id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
        $orderHasItem->addColumn('order_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $orderHasItem->addColumn('product_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $orderHasItem->addColumn('quantity', 'integer', ['unsigned' => true, 'notnull' => true, 'default' => 1]);
        $orderHasItem->addColumn('price', 'float', array('notnull' => false, 'default' => 0));
        $orderHasItem->addColumn('discount', 'float', array('notnull' => false));
        $orderHasItem->addColumn('order_num', 'integer', ['unsigned' => true, 'notnull' => true, 'default' => 0]);
        $orderHasItem->setPrimaryKey(['id']);
        $orderHasItem->addIndex(['order_id', 'product_id']);
        $orderHasItem->addForeignKeyConstraint($order, ['order_id'], ['id']);
        $orderHasItem->addForeignKeyConstraint($schema->getTable('product_product'), ['product_id'], ['id']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('order_order_has_item');
        $schema->dropTable('order_order');
    }
}
