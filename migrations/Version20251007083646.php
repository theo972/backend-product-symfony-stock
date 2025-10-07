<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251007083646 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sale_order (id INT UNSIGNED AUTO_INCREMENT NOT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, status VARCHAR(100) NOT NULL, total INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_25F5CB1BDE12AB56 (created_by), INDEX IDX_25F5CB1B16FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sale_order ADD CONSTRAINT FK_25F5CB1BDE12AB56 FOREIGN KEY (created_by) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sale_order ADD CONSTRAINT FK_25F5CB1B16FE72E1 FOREIGN KEY (updated_by) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE saleOrder DROP FOREIGN KEY FK_D583B86216FE72E1');
        $this->addSql('ALTER TABLE saleOrder DROP FOREIGN KEY FK_D583B862DE12AB56');
        $this->addSql('DROP TABLE saleOrder');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB08D9F6D38 FOREIGN KEY (order_id) REFERENCES sale_order (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB08D9F6D38');
        $this->addSql('CREATE TABLE saleOrder (id INT UNSIGNED AUTO_INCREMENT NOT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, status VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, total INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_D583B86216FE72E1 (updated_by), INDEX IDX_D583B862DE12AB56 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE saleOrder ADD CONSTRAINT FK_D583B86216FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE saleOrder ADD CONSTRAINT FK_D583B862DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sale_order DROP FOREIGN KEY FK_25F5CB1BDE12AB56');
        $this->addSql('ALTER TABLE sale_order DROP FOREIGN KEY FK_25F5CB1B16FE72E1');
        $this->addSql('DROP TABLE sale_order');
    }
}
