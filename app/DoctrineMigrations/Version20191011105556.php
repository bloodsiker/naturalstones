<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20191011105556
 */
final class Version20191011105556 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return "Add count book (GenreBundle)";
    }

    public function up(Schema $schema) : void
    {
        $genres = $schema->getTable('genres');
        $genres->addColumn('count_book', 'integer', array('unsigned' => true, 'notnull' => true));
    }

    public function down(Schema $schema) : void
    {
        $genre = $schema->getTable('genres');
        $genre->dropColumn('count_book');
    }
}
