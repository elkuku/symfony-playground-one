<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211205012613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE store_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE store (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE store_tag (store_id INT NOT NULL, tag_id INT NOT NULL, PRIMARY KEY(store_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_25D6D4AB092A811 ON store_tag (store_id)');
        $this->addSql('CREATE INDEX IDX_25D6D4ABAD26311 ON store_tag (tag_id)');
        $this->addSql('CREATE TABLE tag (id INT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE store_tag ADD CONSTRAINT FK_25D6D4AB092A811 FOREIGN KEY (store_id) REFERENCES store (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE store_tag ADD CONSTRAINT FK_25D6D4ABAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE store_tag DROP CONSTRAINT FK_25D6D4AB092A811');
        $this->addSql('ALTER TABLE store_tag DROP CONSTRAINT FK_25D6D4ABAD26311');
        $this->addSql('DROP SEQUENCE store_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tag_id_seq CASCADE');
        $this->addSql('DROP TABLE store');
        $this->addSql('DROP TABLE store_tag');
        $this->addSql('DROP TABLE tag');
    }
}
