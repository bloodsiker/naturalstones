<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191101134345 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return "Add book to order (OrderBundle)";
    }

    public function up(Schema $schema) : void
    {
        $genres = $schema->getTable('order_board');
        $genres->addColumn('book_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $genres->addIndex(['book_id']);
        $genres->addForeignKeyConstraint($schema->getTable('books'), ['book_id'], ['id'], ['onDelete' => 'set null']);
    }

    public function down(Schema $schema) : void
    {
        $genre = $schema->getTable('order_board');
        $genre->dropColumn('book_id');
    }
}
