<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211220114640 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return "Add link instagram (OrderBundle)";
    }

    public function up(Schema $schema) : void
    {
        $genres = $schema->getTable('order_order');
        $genres->addColumn('instagram', 'string', ['length' => 255, 'notnull' => false]);
    }

    public function down(Schema $schema) : void
    {
        $genre = $schema->getTable('order_order');
        $genre->dropColumn('instagram');
    }
}
