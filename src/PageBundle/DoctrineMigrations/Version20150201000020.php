<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150201000020 extends AbstractMigration
{
    /** @var array */
    private $metadata = [];

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Alter table page_site delete columns (title, meta_keywords, meta_description), and create table page_site_translations';
    }

    /**
     * @param Schema $schema
     * @throws \PDOException
     */
    public function preUp(Schema $schema)
    {
        $stmt = $this->connection->prepare('
            SELECT id, title, meta_keywords, meta_description
              FROM page_site
            WHERE title IS NOT NULL
              OR meta_keywords IS NOT NULL
              OR meta_description IS NOT NULL
        ');

        if (!$stmt->execute()) {
            throw new \PDOException('Error select metadata from "page_site" ' . __METHOD__);
        }

        $this->metadata = $stmt->fetchAll();
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $siteTrans = $schema->createTable('page_site_translation');

        $siteTrans->addColumn('id', 'integer', ['autoincrement' => true, 'unsigned' => true, 'notnull' => true]);
        $siteTrans->addColumn('translatable_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $siteTrans->addColumn('locale', 'string', ['length' => 255, 'notnull' => true]);
        $siteTrans->addColumn('title', 'string', ['length' => 255, 'notnull' => false]);
        $siteTrans->addColumn('meta_keywords', 'text', ['notnull' => false]);
        $siteTrans->addColumn('meta_description', 'text', ['notnull' => false]);

        $siteTrans->setPrimaryKey(['id']);

        $siteTrans->addUniqueIndex(
            ['translatable_id', 'locale'],
            'UNIQ_page_site_translation__translatable_id__locale'
        );

        $siteTrans->addForeignKeyConstraint(
            $schema->getTable('page_site'),
            ['translatable_id'],
            ['id'],
            ['onDelete' => 'cascade'],
            'FK_page_site__page_site_translation__id__translatable_id'
        );

        $pageSite = $schema->getTable('page_site');
        $pageSite->dropColumn('title');
        $pageSite->dropColumn('meta_keywords');
        $pageSite->dropColumn('meta_description');
        $pageSite->dropColumn('top_counter');
        $pageSite->dropColumn('bottom_counter');
    }

    /**
     * @param Schema $schema
     */
    public function postUp(Schema $schema)
    {
        $stmt = $this->connection->prepare('
            INSERT INTO page_site_translation
              (translatable_id, title, meta_keywords, meta_description, locale)
            VALUES
              (:translatable_id, :title, :meta_keywords, :meta_description, :locale)
            ON DUPLICATE KEY UPDATE
              translatable_id=VALUES(translatable_id),
              title=VALUES(title),
              meta_keywords=VALUES(meta_keywords),
              meta_description=VALUES(meta_description),
              locale=VALUES(locale)
        ');

        foreach ($this->metadata as $item) {
            array_map(function ($locale) use ($item, $stmt) {
                $stmt->bindValue(':translatable_id', $item['id'], \PDO::PARAM_INT);
                $stmt->bindValue(':title', $item['title'], \PDO::PARAM_STR);
                $stmt->bindValue(':meta_keywords', $item['meta_keywords'], \PDO::PARAM_STR);
                $stmt->bindValue(':meta_description', $item['meta_description'], \PDO::PARAM_STR);
                $stmt->bindValue(':locale', $locale, \PDO::PARAM_STR);
                if (!$stmt->execute()) {
                    throw new \PDOException('Error insert metadata from "page_site"');
                }
            }, ['ru', 'uk']);
        }
    }

    /**
     * @param Schema $schema
     */
    public function preDown(Schema $schema)
    {
        $stmt = $this->connection->prepare('
            SELECT translatable_id AS id, title, meta_keywords, meta_description
              FROM page_site_translation
            WHERE title IS NOT NULL
              OR meta_keywords IS NOT NULL
              OR meta_description IS NOT NULL
        ');

        if (!$stmt->execute()) {
            throw new \PDOException('Error select metadata from "page_site_translation" ' . __METHOD__);
        }

        $this->metadata = $stmt->fetchAll();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $table = $schema->getTable('page_site_translation');

        if ($table->hasForeignKey('FK_page_site__page_site_translation__id__translatable_id')) {
            $table->removeForeignKey('FK_page_site__page_site_translation__id__translatable_id');
        }

        $schema->dropTable('page_site_translation');

        $pageSite = $schema->getTable('page_site');
        $pageSite->addColumn('title', 'string', ['length' => 64, 'notnull' => false]);
        $pageSite->addColumn('meta_keywords', 'string', ['length' => 255, 'notnull' => false]);
        $pageSite->addColumn('meta_description', 'string', ['length' => 255, 'notnull' => false]);
    }

    /**
     * @param Schema $schema
     */
    public function postDown(Schema $schema)
    {
        $stmt = $this->connection->prepare('
            UPDATE page_site
                SET
                  title = :title,
                  meta_keywords = :meta_keywords,
                  meta_description = :meta_description
            WHERE id = :id
        ');

        foreach ($this->metadata as $item) {
            $stmt->bindValue(':id', $item['id'], \PDO::PARAM_INT);
            $stmt->bindValue(':title', $item['title'], \PDO::PARAM_STR);
            $stmt->bindValue(':meta_keywords', $item['meta_keywords'], \PDO::PARAM_STR);
            $stmt->bindValue(':meta_description', $item['meta_description'], \PDO::PARAM_STR);
            if (!$stmt->execute()) {
                throw new \PDOException('Error insert metadata from "page_site" ' . __METHOD__);
            }
        }
    }
}
