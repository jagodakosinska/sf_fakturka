<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210827071552 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bank_account DROP CONSTRAINT fk_53a23e0aa76ed395');
        $this->addSql('DROP INDEX idx_53a23e0aa76ed395');
        $this->addSql('ALTER TABLE bank_account RENAME COLUMN user_id TO owner_id');
        $this->addSql('ALTER TABLE bank_account ADD CONSTRAINT FK_53A23E0A7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_53A23E0A7E3C61F9 ON bank_account (owner_id)');
        $this->addSql('ALTER TABLE customer DROP CONSTRAINT fk_81398e09a76ed395');
        $this->addSql('DROP INDEX idx_81398e09a76ed395');
        $this->addSql('ALTER TABLE customer RENAME COLUMN user_id TO vendor_id');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09F603EE73 FOREIGN KEY (vendor_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_81398E09F603EE73 ON customer (vendor_id)');
        $this->addSql('ALTER TABLE customer_address RENAME COLUMN valid_date TO valid_from');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT fk_90651744a76ed395');
        $this->addSql('DROP INDEX idx_90651744a76ed395');
        $this->addSql('ALTER TABLE invoice ALTER to_pay TYPE INT');
        $this->addSql('ALTER TABLE invoice ALTER to_pay DROP DEFAULT');
        $this->addSql('ALTER TABLE invoice ALTER paid TYPE INT');
        $this->addSql('ALTER TABLE invoice ALTER paid DROP DEFAULT');
        $this->addSql('ALTER TABLE invoice ALTER amount TYPE INT');
        $this->addSql('ALTER TABLE invoice ALTER amount DROP DEFAULT');
        $this->addSql('ALTER TABLE invoice RENAME COLUMN user_id TO vendor_id');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744F603EE73 FOREIGN KEY (vendor_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_90651744F603EE73 ON invoice (vendor_id)');
        $this->addSql('ALTER TABLE invoice_item DROP is_service');
        $this->addSql('ALTER TABLE invoice_item ALTER quantity TYPE INT');
        $this->addSql('ALTER TABLE invoice_item ALTER quantity DROP DEFAULT');
        $this->addSql('ALTER TABLE invoice_item ALTER vat_rate TYPE INT');
        $this->addSql('ALTER TABLE invoice_item ALTER vat_rate DROP DEFAULT');
        $this->addSql('ALTER TABLE invoice_item ALTER unit_price TYPE INT');
        $this->addSql('ALTER TABLE invoice_item ALTER unit_price DROP DEFAULT');
        $this->addSql('ALTER TABLE invoice_item ALTER total_price TYPE INT');
        $this->addSql('ALTER TABLE invoice_item ALTER total_price DROP DEFAULT');
        $this->addSql('ALTER TABLE invoice_item RENAME COLUMN _order TO item_order');
        $this->addSql('ALTER TABLE item ADD vendor_id INT NOT NULL');
        $this->addSql('ALTER TABLE item DROP value');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251EF603EE73 FOREIGN KEY (vendor_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_1F1B251EF603EE73 ON item (vendor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE bank_account DROP CONSTRAINT FK_53A23E0A7E3C61F9');
        $this->addSql('DROP INDEX IDX_53A23E0A7E3C61F9');
        $this->addSql('ALTER TABLE bank_account RENAME COLUMN owner_id TO user_id');
        $this->addSql('ALTER TABLE bank_account ADD CONSTRAINT fk_53a23e0aa76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_53a23e0aa76ed395 ON bank_account (user_id)');
        $this->addSql('ALTER TABLE customer DROP CONSTRAINT FK_81398E09F603EE73');
        $this->addSql('DROP INDEX IDX_81398E09F603EE73');
        $this->addSql('ALTER TABLE customer RENAME COLUMN vendor_id TO user_id');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT fk_81398e09a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_81398e09a76ed395 ON customer (user_id)');
        $this->addSql('ALTER TABLE customer_address RENAME COLUMN valid_from TO valid_date');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT FK_90651744F603EE73');
        $this->addSql('DROP INDEX IDX_90651744F603EE73');
        $this->addSql('ALTER TABLE invoice ALTER to_pay TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE invoice ALTER to_pay DROP DEFAULT');
        $this->addSql('ALTER TABLE invoice ALTER paid TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE invoice ALTER paid DROP DEFAULT');
        $this->addSql('ALTER TABLE invoice ALTER amount TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE invoice ALTER amount DROP DEFAULT');
        $this->addSql('ALTER TABLE invoice RENAME COLUMN vendor_id TO user_id');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT fk_90651744a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_90651744a76ed395 ON invoice (user_id)');
        $this->addSql('ALTER TABLE invoice_item ADD is_service BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE invoice_item ALTER quantity TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE invoice_item ALTER quantity DROP DEFAULT');
        $this->addSql('ALTER TABLE invoice_item ALTER vat_rate TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE invoice_item ALTER vat_rate DROP DEFAULT');
        $this->addSql('ALTER TABLE invoice_item ALTER unit_price TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE invoice_item ALTER unit_price DROP DEFAULT');
        $this->addSql('ALTER TABLE invoice_item ALTER total_price TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE invoice_item ALTER total_price DROP DEFAULT');
        $this->addSql('ALTER TABLE invoice_item RENAME COLUMN item_order TO _order');
        $this->addSql('ALTER TABLE item DROP CONSTRAINT FK_1F1B251EF603EE73');
        $this->addSql('DROP INDEX IDX_1F1B251EF603EE73');
        $this->addSql('ALTER TABLE item ADD value DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE item DROP vendor_id');
    }
}
