<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202302232030278 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Article (ArticleBundle)';
    }

    public function up(Schema $schema) : void
    {
        $category = $schema->createTable('article_category');
        $category->addColumn('id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
        $category->addColumn('is_active', 'boolean', ['notnull' => true]);
        $category->addColumn('slug', 'string', array('length' => 100, 'notnull' => true));
        $category->addColumn('created_at', 'datetime', array('notnull' => true));
        $category->addColumn('updated_at', 'datetime', array('notnull' => false));
        $category->setPrimaryKey(['id']);

        $categoryTranslation = $schema->createTable('article_category_translation');
        $categoryTranslation->addColumn('id', 'integer', ['notnull' => true, 'autoincrement' => true]);
        $categoryTranslation->addColumn('translatable_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $categoryTranslation->addColumn('name', 'string', ['length' => 255, 'notnull' => false]);
        $categoryTranslation->addColumn('locale', 'string', ['length' => 2, 'notnull' => true]);
        $categoryTranslation->setPrimaryKey(['id']);
        $categoryTranslation->addUniqueIndex(['translatable_id', 'locale']);
        $categoryTranslation->addForeignKeyConstraint($category, ['translatable_id'], ['id'], ['onDelete' => 'cascade']);


        $article = $schema->createTable('article_article');
        $article->addColumn('id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
        $article->addColumn('image_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $article->addColumn('category_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $article->addColumn('is_active', 'boolean', ['notnull' => true]);
        $article->addColumn('slug', 'string', array('length' => 100, 'notnull' => true));
        $article->addColumn('views', 'integer', array('unsigned' => true, 'notnull' => false, 'default' => 0));
        $article->addColumn('created_at', 'datetime', array('notnull' => true));
        $article->addColumn('updated_at', 'datetime', array('notnull' => false));
        $article->setPrimaryKey(['id']);
        $article->addIndex(['image_id', 'category_id']);
        $article->addForeignKeyConstraint($schema->getTable('media_image'), ['image_id'], ['id'], ['onDelete' => 'set null']);
        $article->addForeignKeyConstraint($category, ['category_id'], ['id'], ['onDelete' => 'set null']);

        $articleTranslation = $schema->createTable('article_article_translation');
        $articleTranslation->addColumn('id', 'integer', ['notnull' => true, 'autoincrement' => true]);
        $articleTranslation->addColumn('translatable_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $articleTranslation->addColumn('title', 'string', ['length' => 255, 'notnull' => false]);
        $articleTranslation->addColumn('short_description', 'text', ['length' => 65535, 'notnull' => false]);
        $articleTranslation->addColumn('description', 'text', ['length' => 65535, 'notnull' => false]);
        $articleTranslation->addColumn('locale', 'string', ['length' => 2, 'notnull' => true]);
        $articleTranslation->setPrimaryKey(['id']);
        $articleTranslation->addUniqueIndex(['translatable_id', 'locale']);
        $articleTranslation->addForeignKeyConstraint($article, ['translatable_id'], ['id'], ['onDelete' => 'cascade']);

        $articleTags = $schema->createTable('article_article_tags');
        $articleTags->addColumn('article_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $articleTags->addColumn('tag_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $articleTags->addIndex(['article_id', 'tag_id']);
        $articleTags->addForeignKeyConstraint($schema->getTable('article_article'), ['article_id'], ['id'], ['onDelete' => 'cascade']);
        $articleTags->addForeignKeyConstraint($schema->getTable('share_tags'), ['tag_id'], ['id'], ['onDelete' => 'cascade']);
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('article_article_tags');

        $schema->dropTable('article_category_translation');
        $schema->dropTable('article_category');

        $schema->dropTable('article_article_translation');
        $schema->dropTable('article_article');
    }
}
