<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210825123120 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE bank_account_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE customer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE customer_address_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE invoice_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE invoice_item_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE item_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE paid_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE bank_account (id INT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, number VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_53A23E0AA76ED395 ON bank_account (user_id)');
        $this->addSql('CREATE TABLE customer (id INT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, nip VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_81398E09A76ED395 ON customer (user_id)');
        $this->addSql('CREATE TABLE customer_address (id INT NOT NULL, customer_id INT NOT NULL, city VARCHAR(255) NOT NULL, zip_code VARCHAR(255) NOT NULL, street VARCHAR(255) NOT NULL, is_main BOOLEAN NOT NULL, valid_date DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1193CB3F9395C3F3 ON customer_address (customer_id)');
        $this->addSql('COMMENT ON COLUMN customer_address.valid_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('CREATE TABLE invoice (id INT NOT NULL, user_id INT NOT NULL, paid_type_id INT NOT NULL, number VARCHAR(255) NOT NULL, sell_date DATE NOT NULL, issue_date DATE NOT NULL, place VARCHAR(255) NOT NULL, to_pay DOUBLE PRECISION NOT NULL, paid DOUBLE PRECISION NOT NULL, amount DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_90651744A76ED395 ON invoice (user_id)');
        $this->addSql('CREATE INDEX IDX_90651744369E43F6 ON invoice (paid_type_id)');
        $this->addSql('COMMENT ON COLUMN invoice.sell_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN invoice.issue_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('CREATE TABLE invoice_item (id INT NOT NULL, invoice_id INT NOT NULL, item_id INT NOT NULL, is_service BOOLEAN NOT NULL, item_description TEXT NOT NULL, quantity DOUBLE PRECISION NOT NULL, quantity_type VARCHAR(255) NOT NULL, vat_rate DOUBLE PRECISION NOT NULL, unit_price DOUBLE PRECISION NOT NULL, total_price DOUBLE PRECISION NOT NULL, _order INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1DDE477B2989F1FD ON invoice_item (invoice_id)');
        $this->addSql('CREATE INDEX IDX_1DDE477B126F525E ON invoice_item (item_id)');
        $this->addSql('CREATE TABLE item (id INT NOT NULL, name TEXT NOT NULL, description VARCHAR(255) NOT NULL, value DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE paid_type (id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('ALTER TABLE bank_account ADD CONSTRAINT FK_53A23E0AA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE customer_address ADD CONSTRAINT FK_1193CB3F9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744369E43F6 FOREIGN KEY (paid_type_id) REFERENCES paid_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice_item ADD CONSTRAINT FK_1DDE477B2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice_item ADD CONSTRAINT FK_1DDE477B126F525E FOREIGN KEY (item_id) REFERENCES item (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE customer_address DROP CONSTRAINT FK_1193CB3F9395C3F3');
        $this->addSql('ALTER TABLE invoice_item DROP CONSTRAINT FK_1DDE477B2989F1FD');
        $this->addSql('ALTER TABLE invoice_item DROP CONSTRAINT FK_1DDE477B126F525E');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT FK_90651744369E43F6');
        $this->addSql('ALTER TABLE bank_account DROP CONSTRAINT FK_53A23E0AA76ED395');
        $this->addSql('ALTER TABLE customer DROP CONSTRAINT FK_81398E09A76ED395');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT FK_90651744A76ED395');
        $this->addSql('DROP SEQUENCE bank_account_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE customer_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE customer_address_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE invoice_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE invoice_item_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE item_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE paid_type_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP TABLE bank_account');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE customer_address');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE invoice_item');
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE paid_type');
        $this->addSql('DROP TABLE "user"');
    }
}
