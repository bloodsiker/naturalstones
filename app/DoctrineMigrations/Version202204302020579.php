<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202204302020579 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $product = $schema->getTable('product_product');
        $product->addColumn('percent', 'float', array('notnull' => false, 'default' => 0));
    }

    public function down(Schema $schema) : void
    {
        $product = $schema->getTable('product_product');
        $product->dropColumn('percent');
    }
}
