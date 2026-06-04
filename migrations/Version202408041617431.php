<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202408041617431 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $stoneHasConstructor = $schema->createTable('share_stone_has_constructor');
        $stoneHasConstructor->addColumn('id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
        $stoneHasConstructor->addColumn('stone_id', 'integer', ['unsigned' => true, 'notnull' => true]);
        $stoneHasConstructor->addColumn('image_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $stoneHasConstructor->addColumn('size', 'string', ['length' => 20, 'notnull' => true]);
        $stoneHasConstructor->addColumn('order_num', 'integer', ['length' => 11, 'notnull' => true, 'default' => 1]);
        $stoneHasConstructor->setPrimaryKey(['id']);
        $stoneHasConstructor->addIndex(['stone_id', 'image_id']);
        $stoneHasConstructor->addForeignKeyConstraint($schema->getTable('share_stones'), ['stone_id'], ['id'], ['onDelete' => 'restrict']);
        $stoneHasConstructor->addForeignKeyConstraint($schema->getTable('media_image'), ['image_id'], ['id'], ['onDelete' => 'set null']);

        $stone = $schema->getTable('share_stones');
        $stone->addColumn('is_show_constructor', 'boolean', ['notnull' => true, 'default' => 0]);
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('share_stone_has_constructor');
    }
}
