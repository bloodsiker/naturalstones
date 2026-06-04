<?php

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version202606021800000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add admin-managed menu sections and seed the existing category menu';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE app_menu_section (id INT UNSIGNED AUTO_INCREMENT NOT NULL, is_active TINYINT(1) NOT NULL, order_num INT DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_menu_section_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT UNSIGNED DEFAULT NULL, title VARCHAR(255) NOT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_F99F42732C2AC5D3 (translatable_id), UNIQUE INDEX app_menu_section_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_menu_item (id INT UNSIGNED AUTO_INCREMENT NOT NULL, menu_section_id INT UNSIGNED NOT NULL, category_id INT UNSIGNED NOT NULL, order_num INT DEFAULT 0 NOT NULL, INDEX IDX_7F3B8A65F98E57A8 (menu_section_id), INDEX IDX_7F3B8A6512469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE app_menu_section_translation ADD CONSTRAINT FK_MENU_SECTION_TRANSLATION FOREIGN KEY (translatable_id) REFERENCES app_menu_section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE app_menu_item ADD CONSTRAINT FK_MENU_ITEM_SECTION FOREIGN KEY (menu_section_id) REFERENCES app_menu_section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE app_menu_item ADD CONSTRAINT FK_MENU_ITEM_CATEGORY FOREIGN KEY (category_id) REFERENCES product_category (id) ON DELETE CASCADE');

        $this->addSql('INSERT INTO app_menu_section (id, is_active, order_num) VALUES (1, 1, 600), (2, 1, 500), (3, 1, 400), (4, 1, 300), (5, 1, 200), (6, 1, 100)');
        $this->addSql("INSERT INTO app_menu_section_translation (translatable_id, locale, title) VALUES
            (1, 'uk', 'Індивідуальні замовлення'), (1, 'ru', 'Индивидуальные заказы'),
            (2, 'uk', 'Гематит'), (2, 'ru', 'Гематит'),
            (3, 'uk', 'Прикраси'), (3, 'ru', 'Украшения'),
            (4, 'uk', 'Фурнітура'), (4, 'ru', 'Фурнитура'),
            (5, 'uk', 'Подарункові упаковки'), (5, 'ru', 'Подарочные упаковки'),
            (6, 'uk', 'Скребки Гуаша'), (6, 'ru', 'Скребки Гуаша')");
        $this->addSql('INSERT INTO app_menu_item (menu_section_id, category_id, order_num) SELECT 1, id, order_num FROM product_category WHERE type = 3');
        $this->addSql('INSERT INTO app_menu_item (menu_section_id, category_id, order_num) SELECT 2, id, order_num FROM product_category WHERE type = 6');
        $this->addSql('INSERT INTO app_menu_item (menu_section_id, category_id, order_num) SELECT 3, id, order_num FROM product_category WHERE type = 1');
        $this->addSql('INSERT INTO app_menu_item (menu_section_id, category_id, order_num) SELECT 4, id, order_num FROM product_category WHERE type = 2');
        $this->addSql('INSERT INTO app_menu_item (menu_section_id, category_id, order_num) SELECT 5, id, order_num FROM product_category WHERE type = 4');
        $this->addSql('INSERT INTO app_menu_item (menu_section_id, category_id, order_num) SELECT 6, id, order_num FROM product_category WHERE type = 5');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE app_menu_item');
        $this->addSql('DROP TABLE app_menu_section_translation');
        $this->addSql('DROP TABLE app_menu_section');
    }
}
