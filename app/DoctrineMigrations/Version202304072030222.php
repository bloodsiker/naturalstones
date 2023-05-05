<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202304072030222 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return 'WheelSpin (WheelSpinBundle)';
    }

    public function up(Schema $schema) : void
    {
        $wheel = $schema->createTable('wheel_spin');
        $wheel->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $wheel->addColumn('min_sum', 'float', array('notnull' => false, 'default' => 0));
        $wheel->addColumn('max_sum', 'float', array('notnull' => false, 'default' => 0));
        $wheel->addColumn('is_active', 'boolean', array('notnull' => true));
        $wheel->setPrimaryKey(['id']);

        $wheelOption = $schema->createTable('wheel_spin_option');
        $wheelOption->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $wheelOption->addColumn('product_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $wheelOption->addColumn('image_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $wheelOption->addColumn('is_active', 'boolean', array('notnull' => true));
        $wheelOption->setPrimaryKey(['id']);
        $wheelOption->addIndex(['product_id', 'image_id']);
        $wheelOption->addForeignKeyConstraint($schema->getTable('product_product'), ['product_id'], ['id'], ['onDelete' => 'cascade']);
        $wheelOption->addForeignKeyConstraint($schema->getTable('media_image'), ['image_id'], ['id'], ['onDelete' => 'set null']);


        $wheelTranslation = $schema->createTable('wheel_spin_option_translation');
        $wheelTranslation->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $wheelTranslation->addColumn('translatable_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $wheelTranslation->addColumn('title', 'string', ['length' => 255, 'notnull' => false]);
        $wheelTranslation->addColumn('locale', 'string', ['length' => 2, 'notnull' => true]);
        $wheelTranslation->setPrimaryKey(['id']);
        $wheelTranslation->addUniqueIndex(['translatable_id', 'locale']);
        $wheelTranslation->addForeignKeyConstraint($wheelOption, ['translatable_id'], ['id'], ['onDelete' => 'cascade']);

        $wheelHasOption = $schema->createTable('wheel_spin_has_option');
        $wheelHasOption->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $wheelHasOption->addColumn('wheel_spin_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $wheelHasOption->addColumn('wheel_spin_option_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $wheelHasOption->addColumn('valuation', 'integer', array('notnull' => false, 'default' => 0));
        $wheelHasOption->addColumn('percent', 'float', array('notnull' => false, 'default' => 0));
        $wheelHasOption->addColumn('colour', 'string', array('length' => 100, 'notnull' => true));
        $wheelHasOption->addColumn('degrees', 'string', ['length' => 255, 'notnull' => true]);
        $wheelHasOption->addColumn('label', 'string', ['length' => 255, 'notnull' => true]);
        $wheelHasOption->addColumn('order_num', 'integer', array('length' => 11, 'notnull' => true, 'default' => 1));
        $wheelHasOption->setPrimaryKey(['id']);
        $wheelHasOption->addIndex(['wheel_spin_id', 'wheel_spin_option_id']);
        $wheelHasOption->addForeignKeyConstraint($wheel, ['wheel_spin_id'], ['id'], ['onDelete' => 'restrict']);
        $wheelHasOption->addForeignKeyConstraint($wheelOption, ['wheel_spin_option_id'], ['id'], ['onDelete' => 'restrict']);

        $order = $schema->getTable('order_order');
        $order->addColumn('wheel_spin_option_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $order->addColumn('spin_prize', 'string', ['length' => 255, 'notnull' => false]);
        $order->addColumn('secret', 'string', ['length' => 100, 'notnull' => true]);
        $order->addColumn('is_spin', 'boolean', ['notnull' => true]);
        $order->addIndex(['wheel_spin_option_id']);
        $order->addForeignKeyConstraint($schema->getTable('wheel_spin_option'), ['wheel_spin_option_id'], ['id'], ['onDelete' => 'cascade']);

        $video = $schema->createTable('media_video');
        $video->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $video->addColumn('description', 'string', array('length' => 255, 'notnull' => false));
        $video->addColumn('path', 'string', array('length' => 255, 'notnull' => true));
        $video->addColumn('thumb', 'string', array('length' => 255, 'notnull' => false));
        $video->addColumn('mime_type', 'string', array('length' => 100, 'notnull' => true));
        $video->addColumn('size', 'integer', array('unsigned' => true, 'notnull' => false));
        $video->addColumn('width', 'integer', array('unsigned' => true, 'notnull' => false));
        $video->addColumn('height', 'integer', array('unsigned' => true, 'notnull' => false));
        $video->addColumn('is_active', 'boolean', array('notnull' => true));
        $video->addColumn('created_by', 'integer', array('unsigned' => true, 'notnull' => false));
        $video->addColumn('created_at', 'datetime', array('notnull' => true));
        $video->addColumn('updated_at', 'datetime', array('notnull' => true));
        $video->setPrimaryKey(['id']);
        $video->addIndex(['created_by']);
        $video->addForeignKeyConstraint($schema->getTable('user_users'), ['created_by'], ['id'], ['onDelete' => 'set null']);

        $productVideo = $schema->createTable('product_product_has_video');
        $productVideo->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $productVideo->addColumn('product_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $productVideo->addColumn('video_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $productVideo->addColumn('order_num', 'integer', array('length' => 11, 'notnull' => true, 'default' => 1));
        $productVideo->setPrimaryKey(['id']);
        $productVideo->addIndex(['product_id', 'video_id']);
        $productVideo->addForeignKeyConstraint($video, ['video_id'], ['id'], ['onDelete' => 'restrict']);
        $productVideo->addForeignKeyConstraint($schema->getTable('product_product'), ['product_id'], ['id'], ['onDelete' => 'restrict']);

    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('product_product_has_video');
        $schema->dropTable('media_video');
        $schema->dropTable('wheel_spin_has_option');
        $schema->dropTable('wheel_spin_option_translation');
        $schema->dropTable('wheel_spin_option');
        $schema->dropTable('wheel_spin');
    }
}
