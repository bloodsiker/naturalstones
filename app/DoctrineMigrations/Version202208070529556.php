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
final class Version202208070529556 extends AbstractMigration implements ContainerAwareInterface
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
        $colour = $schema->getTable('share_colours');
        $colour->dropColumn('name');

        $metal = $schema->getTable('share_metals');
        $metal->dropColumn('name');

        $zodiac = $schema->getTable('share_zodiacs');
        $zodiac->dropColumn('name');

        $stone = $schema->getTable('share_stones');
        $stone->dropColumn('name');
        $stone->dropColumn('description');

        $category = $schema->getTable('product_category');
        $category->dropColumn('name');
        $category->dropColumn('description');

        $image = $schema->getTable('main_image');
        $image->dropColumn('title');
        $image->dropColumn('description');

        $tag = $schema->getTable('share_tags');
        $tag->dropColumn('name');

        $text = $schema->getTable('share_text');
        $text->dropColumn('name');
        $text->dropColumn('description');

        $product = $schema->getTable('product_product');
        $product->dropColumn('name');
        $product->dropColumn('description');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
