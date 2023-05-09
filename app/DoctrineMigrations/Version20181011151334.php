<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20181011151334
 */
final class Version20181011151334 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return "Tags (ShareBundle))";
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema) : void
    {
        $tag = $schema->createTable('share_tags');
        $tag->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $tag->addColumn('name', 'string', array('length' => 100, 'notnull' => true));
        $tag->addColumn('slug', 'string', array('length' => 100, 'notnull' => true));
        $tag->addColumn('meta_title', 'string', array('length' => 255, 'notnull' => true));
        $tag->addColumn('metaKeywords', 'text', array('length' => 65535, 'notnull' => false));
        $tag->addColumn('metaDescription', 'text', array('length' => 65535, 'notnull' => false));
        $tag->addColumn('created_at', 'datetime', array('notnull' => true));
        $tag->addColumn('is_active', 'boolean', array('notnull' => true));
        $tag->setPrimaryKey(array('id'));
        $tag->addUniqueIndex(array('slug'));
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $schema->dropTable('share_tags');
    }
}
