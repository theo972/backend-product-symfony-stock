<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251007071734 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB08D9F6D38 FOREIGN KEY (order_id) REFERENCES saleOrder (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE products ADD created_by INT UNSIGNED DEFAULT NULL, ADD updated_by INT UNSIGNED DEFAULT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5ADE12AB56 FOREIGN KEY (created_by) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A16FE72E1 FOREIGN KEY (updated_by) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_B3BA5A5ADE12AB56 ON products (created_by)');
        $this->addSql('CREATE INDEX IDX_B3BA5A5A16FE72E1 ON products (updated_by)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB08D9F6D38');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5ADE12AB56');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A16FE72E1');
        $this->addSql('DROP INDEX IDX_B3BA5A5ADE12AB56 ON products');
        $this->addSql('DROP INDEX IDX_B3BA5A5A16FE72E1 ON products');
        $this->addSql('ALTER TABLE products DROP created_by, DROP updated_by, DROP created_at, DROP updated_at');
    }
}
