<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version20150201000001
 */
class Version20150201000001 extends AbstractMigration
{
    private $pageSite;
    private $pagePage;
    private $pageBlock;
    private $pageSnapshot;

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'PageBundle + NotificationBundle schemas';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this
            ->makePageSite($schema)
            ->makePagePage($schema)
            ->makePageBlock($schema)
            ->makePageSnapshot($schema)
        ;
    }

    /**
     * @param Schema $schema
     */
    public function postUp(Schema $schema)
    {
        $this->addSql('ALTER TABLE `page_snapshot` ADD INDEX `idx_snapshot_route_name` (`route_name` (32), `site_id`);');
        $this->addSql('ALTER TABLE `page_snapshot` ADD INDEX `idx_snapshot_page_alias` (`page_alias` (32), `site_id`);');
        $this->addSql('ALTER TABLE `page_snapshot` ADD INDEX `idx_snapshot_url` (`url` (32), `site_id`);');
        $this->addSql('ALTER TABLE `page_page` ADD INDEX `idx_page_route_name` (`route_name` (32), `site_id`);');
        $this->addSql('ALTER TABLE `page_page` ADD INDEX `idx_page_page_alias` (`page_alias` (32), `site_id`);');
        $this->addSql('ALTER TABLE `page_page` ADD INDEX `idx_page_url` (`url` (32), `site_id`);');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('page_snapshot');
        $schema->dropTable('page_block');
        $schema->dropTable('page_page');
        $schema->dropTable('page_site');
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function makePageSite(Schema $schema)
    {
        $this->pageSite = $pageSite = $schema->createTable('page_site');
        $pageSite->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $pageSite->addColumn('enabled', 'boolean', array('notnull' => true));
        $pageSite->addColumn('name', 'string', array('length' => 255, 'notnull' => true));
        $pageSite->addColumn('relative_path', 'string', array('length' => 255, 'notnull' => false));
        $pageSite->addColumn('host', 'string', array('length' => 255, 'notnull' => true));
        $pageSite->addColumn('enabled_from', 'datetime', array('notnull' => false));
        $pageSite->addColumn('enabled_to', 'datetime', array('notnull' => false));
        $pageSite->addColumn('is_default', 'boolean', array('notnull' => true));
        $pageSite->addColumn('created_at', 'datetime', array('notnull' => true));
        $pageSite->addColumn('updated_at', 'datetime', array('notnull' => true));
        $pageSite->addColumn('locale', 'string', array('length' => 6, 'notnull' => false));
        $pageSite->addColumn('title', 'string', array('length' => 64, 'notnull' => false));
        $pageSite->addColumn('meta_keywords', 'string', array('length' => 255, 'notnull' => false));
        $pageSite->addColumn('meta_description', 'string', array('length' => 255, 'notnull' => false));

        $pageSite->setPrimaryKey(array('id'));

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function makePagePage(Schema $schema)
    {
        $this->pagePage = $pagePage = $schema->createTable('page_page');
        $pagePage->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $pagePage->addColumn('site_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $pagePage->addColumn('parent_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $pagePage->addColumn('target_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $pagePage->addColumn('route_name', 'string', array('length' => 255, 'notnull' => true));
        $pagePage->addColumn('page_alias', 'string', array('length' => 255, 'notnull' => false));
        $pagePage->addColumn('type', 'string', array('length' => 255, 'notnull' => false));
        $pagePage->addColumn('position', 'integer', array('notnull' => true));
        $pagePage->addColumn('enabled', 'boolean', array('notnull' => true));
        $pagePage->addColumn('decorate', 'boolean', array('notnull' => true));
        $pagePage->addColumn('edited', 'boolean', array('notnull' => true));
        $pagePage->addColumn('name', 'string', array('length' => 255, 'notnull' => true));
        $pagePage->addColumn('slug', 'text', array('length' => 4294967295, 'notnull' => false));
        $pagePage->addColumn('url', 'text', array('length' => 4294967295, 'notnull' => false));
        $pagePage->addColumn('custom_url', 'text', array('length' => 4294967295, 'notnull' => false));
        $pagePage->addColumn('request_method', 'string', array('length' => 255, 'notnull' => false));
        $pagePage->addColumn('title', 'string', array('length' => 255, 'notnull' => false));
        $pagePage->addColumn('meta_keyword', 'string', array('length' => 255, 'notnull' => false));
        $pagePage->addColumn('meta_description', 'string', array('length' => 255, 'notnull' => false));
        $pagePage->addColumn('javascript', 'text', array('length' => 4294967295, 'notnull' => false));
        $pagePage->addColumn('stylesheet', 'text', array('length' => 4294967295, 'notnull' => false));
        $pagePage->addColumn('raw_headers', 'text', array('length' => 4294967295, 'notnull' => false));
        $pagePage->addColumn('template', 'string', array('length' => 255, 'notnull' => true));
        $pagePage->addColumn('created_at', 'datetime', array('notnull' => true));
        $pagePage->addColumn('updated_at', 'datetime', array('notnull' => true));
        $pagePage->setPrimaryKey(array('id'));
        $pagePage->addForeignKeyConstraint($pagePage, array('target_id'), array('id'), array('onDelete' => 'cascade'));
        $pagePage->addForeignKeyConstraint($pagePage, array('parent_id'), array('id'), array('onDelete' => 'cascade'));
        $pagePage->addForeignKeyConstraint($this->pageSite, array('site_id'), array('id'), array('onDelete' => 'cascade'));

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function makePageBlock(Schema $schema)
    {
        $this->pageBlock = $pageBlock = $schema->createTable('page_block');
        $pageBlock->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $pageBlock->addColumn('parent_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $pageBlock->addColumn('page_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $pageBlock->addColumn('name', 'string', array('length' => 255, 'notnull' => false));
        $pageBlock->addColumn('type', 'string', array('length' => 64, 'notnull' => true));
        $pageBlock->addColumn('settings', 'text', array('length' => 4294967295, 'notnull' => true, 'comment' => '(DC2Type:json)'));
        $pageBlock->addColumn('enabled', 'boolean', array('notnull' => false));
        $pageBlock->addColumn('position', 'integer', array('notnull' => false));
        $pageBlock->addColumn('created_at', 'datetime', array('notnull' => true));
        $pageBlock->addColumn('updated_at', 'datetime', array('notnull' => true));
        $pageBlock->setPrimaryKey(array('id'));
        $pageBlock->addForeignKeyConstraint($pageBlock, array('parent_id'), array('id'), array('onDelete' => 'cascade'));
        $pageBlock->addForeignKeyConstraint($this->pagePage, array('page_id'), array('id'), array('onDelete' => 'cascade'));

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function makePageSnapshot(Schema $schema)
    {
        $this->pageSnapshot = $pageSnapshot = $schema->createTable('page_snapshot');
        $pageSnapshot->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $pageSnapshot->addColumn('site_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $pageSnapshot->addColumn('page_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $pageSnapshot->addColumn('route_name', 'string', array('length' => 255, 'notnull' => true));
        $pageSnapshot->addColumn('page_alias', 'string', array('length' => 255, 'notnull' => false));
        $pageSnapshot->addColumn('type', 'string', array('length' => 255, 'notnull' => false));
        $pageSnapshot->addColumn('position', 'integer', array('notnull' => true));
        $pageSnapshot->addColumn('enabled', 'boolean', array('notnull' => true));
        $pageSnapshot->addColumn('decorate', 'boolean', array('notnull' => true));
        $pageSnapshot->addColumn('name', 'string', array('length' => 255, 'notnull' => true));
        $pageSnapshot->addColumn('url', 'text', array('length' => 4294967295, 'notnull' => false));
        $pageSnapshot->addColumn('parent_id', 'integer', array('notnull' => false));
        $pageSnapshot->addColumn('target_id', 'integer', array('notnull' => false));
        $pageSnapshot->addColumn('content', 'text', array('length' => 4294967295, 'notnull' => false, 'comment' => '(DC2Type:json)'));
        $pageSnapshot->addColumn('publication_date_start', 'datetime', array('notnull' => false));
        $pageSnapshot->addColumn('publication_date_end', 'datetime', array('notnull' => false));
        $pageSnapshot->addColumn('created_at', 'datetime', array('notnull' => true));
        $pageSnapshot->addColumn('updated_at', 'datetime', array('notnull' => true));
        $pageSnapshot->setPrimaryKey(array('id'));
        $pageSnapshot->addIndex(array('publication_date_start', 'publication_date_end', 'enabled'));
        $pageSnapshot->addForeignKeyConstraint($this->pagePage, array('page_id'), array('id'), array('onDelete' => 'cascade'));
        $pageSnapshot->addForeignKeyConstraint($this->pageSite, array('site_id'), array('id'), array('onDelete' => 'cascade'));

        return $this;
    }
}
