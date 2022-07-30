<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use ShareBundle\Entity\ColourTranslation;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202207140529556 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function up(Schema $schema) : void
    {
        $colourTranslation = $schema->createTable('share_colours_translation');
        $colourTranslation->addColumn('id', 'integer', ['notnull' => true, 'autoincrement' => true]);
        $colourTranslation->addColumn('translatable_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $colourTranslation->addColumn('name', 'string', ['length' => 100, 'notnull' => false]);
        $colourTranslation->addColumn('locale', 'string', ['length' => 2, 'notnull' => true]);
        $colourTranslation->setPrimaryKey(['id']);
        $colourTranslation->addUniqueIndex(['translatable_id', 'locale']);
        $colourTranslation->addForeignKeyConstraint($schema->getTable('share_colours'), ['translatable_id'], ['id'], ['onDelete' => 'cascade']);

        $metalTranslation = $schema->createTable('share_metals_translation');
        $metalTranslation->addColumn('id', 'integer', ['notnull' => true, 'autoincrement' => true]);
        $metalTranslation->addColumn('translatable_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $metalTranslation->addColumn('name', 'string', ['length' => 100, 'notnull' => false]);
        $metalTranslation->addColumn('locale', 'string', ['length' => 2, 'notnull' => true]);
        $metalTranslation->setPrimaryKey(['id']);
        $metalTranslation->addUniqueIndex(['translatable_id', 'locale']);
        $metalTranslation->addForeignKeyConstraint($schema->getTable('share_metals'), ['translatable_id'], ['id'], ['onDelete' => 'cascade']);

        $zodiacTranslation = $schema->createTable('share_zodiacs_translation');
        $zodiacTranslation->addColumn('id', 'integer', ['notnull' => true, 'autoincrement' => true]);
        $zodiacTranslation->addColumn('translatable_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $zodiacTranslation->addColumn('name', 'string', ['length' => 100, 'notnull' => false]);
        $zodiacTranslation->addColumn('locale', 'string', ['length' => 2, 'notnull' => true]);
        $zodiacTranslation->setPrimaryKey(['id']);
        $zodiacTranslation->addUniqueIndex(['translatable_id', 'locale']);
        $zodiacTranslation->addForeignKeyConstraint($schema->getTable('share_zodiacs'), ['translatable_id'], ['id'], ['onDelete' => 'cascade']);

        $stoneTranslation = $schema->createTable('share_stones_translation');
        $stoneTranslation->addColumn('id', 'integer', ['notnull' => true, 'autoincrement' => true]);
        $stoneTranslation->addColumn('translatable_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $stoneTranslation->addColumn('name', 'string', ['length' => 255, 'notnull' => false]);
        $stoneTranslation->addColumn('description', 'text', ['length' => 65535, 'notnull' => false]);
        $stoneTranslation->addColumn('locale', 'string', ['length' => 2, 'notnull' => true]);
        $stoneTranslation->setPrimaryKey(['id']);
        $stoneTranslation->addUniqueIndex(['translatable_id', 'locale']);
        $stoneTranslation->addForeignKeyConstraint($schema->getTable('share_stones'), ['translatable_id'], ['id'], ['onDelete' => 'cascade']);

        $categoryTranslation = $schema->createTable('product_category_translation');
        $categoryTranslation->addColumn('id', 'integer', ['notnull' => true, 'autoincrement' => true]);
        $categoryTranslation->addColumn('translatable_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $categoryTranslation->addColumn('name', 'string', ['length' => 255, 'notnull' => false]);
        $categoryTranslation->addColumn('description', 'text', ['length' => 65535, 'notnull' => false]);
        $categoryTranslation->addColumn('locale', 'string', ['length' => 2, 'notnull' => true]);
        $categoryTranslation->setPrimaryKey(['id']);
        $categoryTranslation->addUniqueIndex(['translatable_id', 'locale']);
        $categoryTranslation->addForeignKeyConstraint($schema->getTable('product_category'), ['translatable_id'], ['id'], ['onDelete' => 'cascade']);

        $imageTranslation = $schema->createTable('main_image_translation');
        $imageTranslation->addColumn('id', 'integer', ['notnull' => true, 'autoincrement' => true]);
        $imageTranslation->addColumn('translatable_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $imageTranslation->addColumn('title', 'string', ['length' => 255, 'notnull' => false]);
        $imageTranslation->addColumn('description', 'text', ['length' => 65535, 'notnull' => false]);
        $imageTranslation->addColumn('locale', 'string', ['length' => 2, 'notnull' => true]);
        $imageTranslation->setPrimaryKey(['id']);
        $imageTranslation->addUniqueIndex(['translatable_id', 'locale']);
        $imageTranslation->addForeignKeyConstraint($schema->getTable('main_image'), ['translatable_id'], ['id'], ['onDelete' => 'cascade']);

        $tagTranslation = $schema->createTable('share_tags_translation');
        $tagTranslation->addColumn('id', 'integer', ['notnull' => true, 'autoincrement' => true]);
        $tagTranslation->addColumn('translatable_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $tagTranslation->addColumn('name', 'string', ['length' => 100, 'notnull' => false]);
        $tagTranslation->addColumn('locale', 'string', ['length' => 2, 'notnull' => true]);
        $tagTranslation->setPrimaryKey(['id']);
        $tagTranslation->addUniqueIndex(['translatable_id', 'locale']);
        $tagTranslation->addForeignKeyConstraint($schema->getTable('share_tags'), ['translatable_id'], ['id'], ['onDelete' => 'cascade']);

        $tagTranslation = $schema->getTable('share_tags');
        $tagTranslation->dropColumn('meta_title');
        $tagTranslation->dropColumn('meta_description');
        $tagTranslation->dropColumn('meta_keywords');

        $textTranslation = $schema->createTable('share_text_translation');
        $textTranslation->addColumn('id', 'integer', ['notnull' => true, 'autoincrement' => true]);
        $textTranslation->addColumn('translatable_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $textTranslation->addColumn('name', 'string', ['length' => 255, 'notnull' => false]);
        $textTranslation->addColumn('description', 'text', ['length' => 65535, 'notnull' => false]);
        $textTranslation->addColumn('locale', 'string', ['length' => 2, 'notnull' => true]);
        $textTranslation->setPrimaryKey(['id']);
        $textTranslation->addUniqueIndex(['translatable_id', 'locale']);
        $textTranslation->addForeignKeyConstraint($schema->getTable('share_text'), ['translatable_id'], ['id'], ['onDelete' => 'cascade']);

        $productTranslation = $schema->createTable('product_product_translation');
        $productTranslation->addColumn('id', 'integer', ['notnull' => true, 'autoincrement' => true]);
        $productTranslation->addColumn('translatable_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $productTranslation->addColumn('name', 'string', ['length' => 255, 'notnull' => false]);
        $productTranslation->addColumn('description', 'text', ['length' => 65535, 'notnull' => false]);
        $productTranslation->addColumn('locale', 'string', ['length' => 2, 'notnull' => true]);
        $productTranslation->setPrimaryKey(['id']);
        $productTranslation->addUniqueIndex(['translatable_id', 'locale']);
        $productTranslation->addForeignKeyConstraint($schema->getTable('product_product'), ['translatable_id'], ['id'], ['onDelete' => 'cascade']);
    }

    /**
     * @param  Schema  $schema
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function postUp(Schema $schema)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $colourRepository = $em->getRepository('ShareBundle:Colour');
        $metalRepository = $em->getRepository('ShareBundle:Metal');
        $zodiacRepository = $em->getRepository('ShareBundle:Zodiac');
        $stoneRepository = $em->getRepository('ShareBundle:Stone');
        $tagRepository = $em->getRepository('ShareBundle:Tag');
        $textRepository = $em->getRepository('ShareBundle:Text');
        $categoryRepository = $em->getRepository('ProductBundle:Category');
        $productRepository = $em->getRepository('ProductBundle:Product');
        $imagesRepository = $em->getRepository('MainImageBundle:MainImage');

        $colours = $colourRepository->findAll();
        foreach ($colours as $color) {
            $color->translate('uk')->setName($color->getName());
            $color->translate('ru')->setName($color->getName());

            $em->persist($color);
            $color->mergeNewTranslations();
        }
        $em->flush();


        $metals = $metalRepository->findAll();
        foreach ($metals as $metal) {
            $metal->translate('uk')->setName($metal->getName());
            $metal->translate('ru')->setName($metal->getName());

            $em->persist($metal);
            $metal->mergeNewTranslations();
        }
        $em->flush();

        $zodiacs = $zodiacRepository->findAll();
        foreach ($zodiacs as $zodiac) {
            $zodiac->translate('uk')->setName($zodiac->getName());
            $zodiac->translate('ru')->setName($zodiac->getName());

            $em->persist($zodiac);
            $zodiac->mergeNewTranslations();
        }
        $em->flush();

        $stones = $stoneRepository->findAll();
        foreach ($stones as $stone) {
            $stone->translate('uk')->setName($stone->getName());
            $stone->translate('uk')->setDescription($stone->getDescription());
            $stone->translate('ru')->setName($stone->getName());
            $stone->translate('ru')->setDescription($stone->getDescription());

            $em->persist($stone);
            $stone->mergeNewTranslations();
        }
        $em->flush();

        $categories = $categoryRepository->findAll();
        foreach ($categories as $category) {
            $category->translate('uk')->setName($category->getName());
            $category->translate('uk')->setDescription($category->getDescription());
            $category->translate('ru')->setName($category->getName());
            $category->translate('ru')->setDescription($category->getDescription());

            $em->persist($category);
            $category->mergeNewTranslations();
        }
        $em->flush();

        $images = $imagesRepository->findAll();
        foreach ($images as $image) {
            $image->translate('uk')->setTitle($image->getTitle());
            $image->translate('uk')->setDescription($image->getDescription());
            $image->translate('ru')->setTitle($image->getTitle());
            $image->translate('ru')->setDescription($image->getDescription());

            $em->persist($image);
            $image->mergeNewTranslations();
        }
        $em->flush();

        $tags = $tagRepository->findAll();
        foreach ($tags as $tag) {
            $tag->translate('uk')->setName($tag->getName());
            $tag->translate('ru')->setName($tag->getName());

            $em->persist($tag);
            $tag->mergeNewTranslations();
        }
        $em->flush();

        $texts = $textRepository->findAll();
        foreach ($texts as $text) {
            $text->translate('uk')->setName($text->getName());
            $text->translate('uk')->setDescription($text->getDescription());
            $text->translate('ru')->setName($text->getName());
            $text->translate('ru')->setDescription($text->getDescription());

            $em->persist($text);
            $text->mergeNewTranslations();
        }
        $em->flush();

        $products = $productRepository->findAll();
        foreach ($products as $product) {
            $product->translate('uk')->setName($product->getName());
            $product->translate('uk')->setDescription($product->getDescription());
            $product->translate('ru')->setName($product->getName());
            $product->translate('ru')->setDescription($product->getDescription());

            $em->persist($product);
            $product->mergeNewTranslations();
        }
        $em->flush();
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
