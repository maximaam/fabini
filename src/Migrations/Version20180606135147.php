<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180606135147 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE fuser (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, zip VARCHAR(16) NOT NULL, city VARCHAR(32) NOT NULL, country VARCHAR(32) NOT NULL, address_shipping VARCHAR(255) NOT NULL, zip_shipping VARCHAR(16) NOT NULL, city_shipping VARCHAR(32) NOT NULL, country_shipping VARCHAR(32) NOT NULL, ip VARCHAR(32) DEFAULT NULL, UNIQUE INDEX UNIQ_7C877CA592FC23A8 (username_canonical), UNIQUE INDEX UNIQ_7C877CA5A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_7C877CA5C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, product_number VARCHAR(128) NOT NULL, product_name VARCHAR(128) NOT NULL, title_de VARCHAR(255) NOT NULL, title_en VARCHAR(255) NOT NULL, description_de LONGTEXT NOT NULL, description_en LONGTEXT NOT NULL, colors LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', sizes LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', price NUMERIC(10, 2) NOT NULL, top_item TINYINT(1) NOT NULL, images LONGTEXT NOT NULL, INDEX IDX_D34A04AD12469DE2 (category_id), INDEX idx_product_name (product_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, name_de VARCHAR(128) NOT NULL, name_en VARCHAR(128) NOT NULL, alias_de VARCHAR(128) NOT NULL, alias_en VARCHAR(128) NOT NULL, description_de LONGTEXT NOT NULL, description_en LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_64C19C1B3BED20D (name_de), UNIQUE INDEX UNIQ_64C19C13D773AC4 (name_en), UNIQUE INDEX UNIQ_64C19C19CFF0439 (alias_de), UNIQUE INDEX UNIQ_64C19C11236ECF0 (alias_en), INDEX IDX_64C19C1727ACA70 (parent_id), INDEX idx_alias_de (alias_de), INDEX idx_alias_en (alias_en), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, title_de VARCHAR(32) NOT NULL, title_en VARCHAR(32) NOT NULL, description_de LONGTEXT NOT NULL, description_en LONGTEXT NOT NULL, slug_de VARCHAR(255) NOT NULL, slug_en VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_140AB620C836C966 (title_de), UNIQUE INDEX UNIQ_140AB62046FF21AF (title_en), INDEX idx_slug_de (slug_de), INDEX idx_slug_en (slug_en), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1727ACA70 FOREIGN KEY (parent_id) REFERENCES category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1727ACA70');
        $this->addSql('DROP TABLE fuser');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE page');
    }
}
