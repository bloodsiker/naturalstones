<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202204222020579 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $shareText = $schema->createTable('share_text');
        $shareText->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $shareText->addColumn('name', 'string', array('length' => 255, 'notnull' => true));
        $shareText->addColumn('description', 'text', array('length' => 65535, 'notnull' => true));
        $shareText->addColumn('is_active', 'boolean', array('notnull' => true));
        $shareText->setPrimaryKey(array('id'));
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('share_text');
    }
}
