<?php

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20150201000001
 */
class Version20150201000001 extends AbstractMigration
{
    private $pageSite;
    private $pagePage;
    private $pageBlock;
    private $pageSnapshot;

    public function getDescription(): string
    {
        return 'PageBundle + NotificationBundle schemas';
    }

    public function up(Schema $schema)
    {
        $this
            ->makePageSite($schema)
            ->makePagePage($schema)
            ->makePageBlock($schema)
            ->makePageSnapshot($schema)
        ;
    }

    public function postUp(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `page_snapshot` ADD INDEX `idx_snapshot_route_name` (`route_name` (32), `site_id`);');
        $this->addSql('ALTER TABLE `page_snapshot` ADD INDEX `idx_snapshot_page_alias` (`page_alias` (32), `site_id`);');
        $this->addSql('ALTER TABLE `page_snapshot` ADD INDEX `idx_snapshot_url` (`url` (32), `site_id`);');
        $this->addSql('ALTER TABLE `page_page` ADD INDEX `idx_page_route_name` (`route_name` (32), `site_id`);');
        $this->addSql('ALTER TABLE `page_page` ADD INDEX `idx_page_page_alias` (`page_alias` (32), `site_id`);');
        $this->addSql('ALTER TABLE `page_page` ADD INDEX `idx_page_url` (`url` (32), `site_id`);');
    }

    public function down(Schema $schema)
    {
        $schema->dropTable('page_snapshot');
        $schema->dropTable('page_block');
        $schema->dropTable('page_page');
        $schema->dropTable('page_site');
    }

    /**
     * @return $this
     */
    private function makePageSite(Schema $schema)
    {
        $this->pageSite = $pageSite = $schema->createTable('page_site');
        $pageSite->addColumn('id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
        $pageSite->addColumn('enabled', 'boolean', ['notnull' => true]);
        $pageSite->addColumn('name', 'string', ['length' => 255, 'notnull' => true]);
        $pageSite->addColumn('relative_path', 'string', ['length' => 255, 'notnull' => false]);
        $pageSite->addColumn('host', 'string', ['length' => 255, 'notnull' => true]);
        $pageSite->addColumn('enabled_from', 'datetime', ['notnull' => false]);
        $pageSite->addColumn('enabled_to', 'datetime', ['notnull' => false]);
        $pageSite->addColumn('is_default', 'boolean', ['notnull' => true]);
        $pageSite->addColumn('created_at', 'datetime', ['notnull' => true]);
        $pageSite->addColumn('updated_at', 'datetime', ['notnull' => true]);
        $pageSite->addColumn('locale', 'string', ['length' => 6, 'notnull' => false]);
        $pageSite->addColumn('title', 'string', ['length' => 64, 'notnull' => false]);
        $pageSite->addColumn('meta_keywords', 'string', ['length' => 255, 'notnull' => false]);
        $pageSite->addColumn('meta_description', 'string', ['length' => 255, 'notnull' => false]);

        $pageSite->setPrimaryKey(['id']);

        return $this;
    }

    /**
     * @return $this
     */
    private function makePagePage(Schema $schema)
    {
        $this->pagePage = $pagePage = $schema->createTable('page_page');
        $pagePage->addColumn('id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
        $pagePage->addColumn('site_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $pagePage->addColumn('parent_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $pagePage->addColumn('target_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $pagePage->addColumn('route_name', 'string', ['length' => 255, 'notnull' => true]);
        $pagePage->addColumn('page_alias', 'string', ['length' => 255, 'notnull' => false]);
        $pagePage->addColumn('type', 'string', ['length' => 255, 'notnull' => false]);
        $pagePage->addColumn('position', 'integer', ['notnull' => true]);
        $pagePage->addColumn('enabled', 'boolean', ['notnull' => true]);
        $pagePage->addColumn('decorate', 'boolean', ['notnull' => true]);
        $pagePage->addColumn('edited', 'boolean', ['notnull' => true]);
        $pagePage->addColumn('name', 'string', ['length' => 255, 'notnull' => true]);
        $pagePage->addColumn('slug', 'text', ['length' => 4294967295, 'notnull' => false]);
        $pagePage->addColumn('url', 'text', ['length' => 4294967295, 'notnull' => false]);
        $pagePage->addColumn('custom_url', 'text', ['length' => 4294967295, 'notnull' => false]);
        $pagePage->addColumn('request_method', 'string', ['length' => 255, 'notnull' => false]);
        $pagePage->addColumn('title', 'string', ['length' => 255, 'notnull' => false]);
        $pagePage->addColumn('meta_keyword', 'string', ['length' => 255, 'notnull' => false]);
        $pagePage->addColumn('meta_description', 'string', ['length' => 255, 'notnull' => false]);
        $pagePage->addColumn('javascript', 'text', ['length' => 4294967295, 'notnull' => false]);
        $pagePage->addColumn('stylesheet', 'text', ['length' => 4294967295, 'notnull' => false]);
        $pagePage->addColumn('raw_headers', 'text', ['length' => 4294967295, 'notnull' => false]);
        $pagePage->addColumn('template', 'string', ['length' => 255, 'notnull' => true]);
        $pagePage->addColumn('created_at', 'datetime', ['notnull' => true]);
        $pagePage->addColumn('updated_at', 'datetime', ['notnull' => true]);
        $pagePage->setPrimaryKey(['id']);
        $pagePage->addForeignKeyConstraint($pagePage, ['target_id'], ['id'], ['onDelete' => 'cascade']);
        $pagePage->addForeignKeyConstraint($pagePage, ['parent_id'], ['id'], ['onDelete' => 'cascade']);
        $pagePage->addForeignKeyConstraint($this->pageSite, ['site_id'], ['id'], ['onDelete' => 'cascade']);

        return $this;
    }

    /**
     * @return $this
     */
    private function makePageBlock(Schema $schema)
    {
        $this->pageBlock = $pageBlock = $schema->createTable('page_block');
        $pageBlock->addColumn('id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
        $pageBlock->addColumn('parent_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $pageBlock->addColumn('page_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $pageBlock->addColumn('name', 'string', ['length' => 255, 'notnull' => false]);
        $pageBlock->addColumn('type', 'string', ['length' => 64, 'notnull' => true]);
        $pageBlock->addColumn('settings', 'text', ['length' => 4294967295, 'notnull' => true, 'comment' => '(DC2Type:json)']);
        $pageBlock->addColumn('enabled', 'boolean', ['notnull' => false]);
        $pageBlock->addColumn('position', 'integer', ['notnull' => false]);
        $pageBlock->addColumn('created_at', 'datetime', ['notnull' => true]);
        $pageBlock->addColumn('updated_at', 'datetime', ['notnull' => true]);
        $pageBlock->setPrimaryKey(['id']);
        $pageBlock->addForeignKeyConstraint($pageBlock, ['parent_id'], ['id'], ['onDelete' => 'cascade']);
        $pageBlock->addForeignKeyConstraint($this->pagePage, ['page_id'], ['id'], ['onDelete' => 'cascade']);

        return $this;
    }

    /**
     * @return $this
     */
    private function makePageSnapshot(Schema $schema)
    {
        $this->pageSnapshot = $pageSnapshot = $schema->createTable('page_snapshot');
        $pageSnapshot->addColumn('id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
        $pageSnapshot->addColumn('site_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $pageSnapshot->addColumn('page_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $pageSnapshot->addColumn('route_name', 'string', ['length' => 255, 'notnull' => true]);
        $pageSnapshot->addColumn('page_alias', 'string', ['length' => 255, 'notnull' => false]);
        $pageSnapshot->addColumn('type', 'string', ['length' => 255, 'notnull' => false]);
        $pageSnapshot->addColumn('position', 'integer', ['notnull' => true]);
        $pageSnapshot->addColumn('enabled', 'boolean', ['notnull' => true]);
        $pageSnapshot->addColumn('decorate', 'boolean', ['notnull' => true]);
        $pageSnapshot->addColumn('name', 'string', ['length' => 255, 'notnull' => true]);
        $pageSnapshot->addColumn('url', 'text', ['length' => 4294967295, 'notnull' => false]);
        $pageSnapshot->addColumn('parent_id', 'integer', ['notnull' => false]);
        $pageSnapshot->addColumn('target_id', 'integer', ['notnull' => false]);
        $pageSnapshot->addColumn('content', 'text', ['length' => 4294967295, 'notnull' => false, 'comment' => '(DC2Type:json)']);
        $pageSnapshot->addColumn('publication_date_start', 'datetime', ['notnull' => false]);
        $pageSnapshot->addColumn('publication_date_end', 'datetime', ['notnull' => false]);
        $pageSnapshot->addColumn('created_at', 'datetime', ['notnull' => true]);
        $pageSnapshot->addColumn('updated_at', 'datetime', ['notnull' => true]);
        $pageSnapshot->setPrimaryKey(['id']);
        $pageSnapshot->addIndex(['publication_date_start', 'publication_date_end', 'enabled']);
        $pageSnapshot->addForeignKeyConstraint($this->pagePage, ['page_id'], ['id'], ['onDelete' => 'cascade']);
        $pageSnapshot->addForeignKeyConstraint($this->pageSite, ['site_id'], ['id'], ['onDelete' => 'cascade']);

        return $this;
    }
}
