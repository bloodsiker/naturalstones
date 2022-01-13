<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202202011029307 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return "Order option (OrderBundle)";
    }

    public function up(Schema $schema) : void
    {
        $order = $schema->getTable('order_order_has_item');
        $order->addColumn('options', 'string', ['length' => 255, 'notnull' => false]);
    }

    public function down(Schema $schema) : void
    {
        $order = $schema->getTable('order_order_has_item');
        $order->dropColumn('options');
    }
}
