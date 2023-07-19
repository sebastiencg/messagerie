<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230718224100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE validity_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE groupement_user (groupement_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(groupement_id, user_id))');
        $this->addSql('CREATE INDEX IDX_31E2BBB2E66695CE ON groupement_user (groupement_id)');
        $this->addSql('CREATE INDEX IDX_31E2BBB2A76ED395 ON groupement_user (user_id)');
        $this->addSql('CREATE TABLE validity (id INT NOT NULL, of_user_id INT DEFAULT NULL, groupe_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_58003FA35A1B2224 ON validity (of_user_id)');
        $this->addSql('CREATE INDEX IDX_58003FA37A45358C ON validity (groupe_id)');
        $this->addSql('ALTER TABLE groupement_user ADD CONSTRAINT FK_31E2BBB2E66695CE FOREIGN KEY (groupement_id) REFERENCES groupement (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE groupement_user ADD CONSTRAINT FK_31E2BBB2A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE validity ADD CONSTRAINT FK_58003FA35A1B2224 FOREIGN KEY (of_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE validity ADD CONSTRAINT FK_58003FA37A45358C FOREIGN KEY (groupe_id) REFERENCES groupement (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE groupement DROP CONSTRAINT fk_c8c9f1527597d3fe');
        $this->addSql('DROP INDEX idx_c8c9f1527597d3fe');
        $this->addSql('ALTER TABLE groupement DROP member_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE validity_id_seq CASCADE');
        $this->addSql('ALTER TABLE groupement_user DROP CONSTRAINT FK_31E2BBB2E66695CE');
        $this->addSql('ALTER TABLE groupement_user DROP CONSTRAINT FK_31E2BBB2A76ED395');
        $this->addSql('ALTER TABLE validity DROP CONSTRAINT FK_58003FA35A1B2224');
        $this->addSql('ALTER TABLE validity DROP CONSTRAINT FK_58003FA37A45358C');
        $this->addSql('DROP TABLE groupement_user');
        $this->addSql('DROP TABLE validity');
        $this->addSql('ALTER TABLE groupement ADD member_id INT NOT NULL');
        $this->addSql('ALTER TABLE groupement ADD CONSTRAINT fk_c8c9f1527597d3fe FOREIGN KEY (member_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_c8c9f1527597d3fe ON groupement (member_id)');
    }
}
