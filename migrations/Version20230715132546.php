<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230715132546 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE friend_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE friend (id INT NOT NULL, of_user1_id INT NOT NULL, of_user2_id INT DEFAULT NULL, validity BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_55EEAC61EA35D260 ON friend (of_user1_id)');
        $this->addSql('CREATE INDEX IDX_55EEAC61F8807D8E ON friend (of_user2_id)');
        $this->addSql('ALTER TABLE friend ADD CONSTRAINT FK_55EEAC61EA35D260 FOREIGN KEY (of_user1_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE friend ADD CONSTRAINT FK_55EEAC61F8807D8E FOREIGN KEY (of_user2_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE friend_id_seq CASCADE');
        $this->addSql('ALTER TABLE friend DROP CONSTRAINT FK_55EEAC61EA35D260');
        $this->addSql('ALTER TABLE friend DROP CONSTRAINT FK_55EEAC61F8807D8E');
        $this->addSql('DROP TABLE friend');
    }
}
