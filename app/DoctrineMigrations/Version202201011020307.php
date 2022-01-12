<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version202201011020307
 */
final class Version202201011020307 extends AbstractMigration
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
        $orderHasItem = $schema->getTable('order_order_has_item');
        $orderHasItem->addColumn('colour_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $orderHasItem->addIndex(['colour_id']);
        $orderHasItem->addForeignKeyConstraint($schema->getTable('share_colours'), ['colour_id'], ['id']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $orderHasItem = $schema->getTable('order_order_has_item');
        $orderHasItem->dropColumn('colour_id');
    }
}
