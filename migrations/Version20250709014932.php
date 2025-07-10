<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250709014932 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE event (id SERIAL NOT NULL, created_by_user_id INT NOT NULL, location_id INT DEFAULT NULL, category_id INT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3BAE0AA77D182D95 ON event (created_by_user_id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA764D218E ON event (location_id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA712469DE2 ON event (category_id)');
        $this->addSql('COMMENT ON COLUMN event.start_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN event.end_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE location (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE participation (id SERIAL NOT NULL, participant_id INT DEFAULT NULL, event_id INT DEFAULT NULL, joined_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AB55E24F9D1C3019 ON participation (participant_id)');
        $this->addSql('CREATE INDEX IDX_AB55E24F71F7E88B ON participation (event_id)');
        $this->addSql('COMMENT ON COLUMN participation.joined_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE reminder (id SERIAL NOT NULL, recipient_id INT NOT NULL, event_id INT NOT NULL, remind_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, sent BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_40374F40E92F8F78 ON reminder (recipient_id)');
        $this->addSql('CREATE INDEX IDX_40374F4071F7E88B ON reminder (event_id)');
        $this->addSql('COMMENT ON COLUMN reminder.remind_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA77D182D95 FOREIGN KEY (created_by_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA764D218E FOREIGN KEY (location_id) REFERENCES location (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA712469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24F9D1C3019 FOREIGN KEY (participant_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24F71F7E88B FOREIGN KEY (event_id) REFERENCES event (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reminder ADD CONSTRAINT FK_40374F40E92F8F78 FOREIGN KEY (recipient_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reminder ADD CONSTRAINT FK_40374F4071F7E88B FOREIGN KEY (event_id) REFERENCES event (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE event DROP CONSTRAINT FK_3BAE0AA77D182D95');
        $this->addSql('ALTER TABLE event DROP CONSTRAINT FK_3BAE0AA764D218E');
        $this->addSql('ALTER TABLE event DROP CONSTRAINT FK_3BAE0AA712469DE2');
        $this->addSql('ALTER TABLE participation DROP CONSTRAINT FK_AB55E24F9D1C3019');
        $this->addSql('ALTER TABLE participation DROP CONSTRAINT FK_AB55E24F71F7E88B');
        $this->addSql('ALTER TABLE reminder DROP CONSTRAINT FK_40374F40E92F8F78');
        $this->addSql('ALTER TABLE reminder DROP CONSTRAINT FK_40374F4071F7E88B');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE participation');
        $this->addSql('DROP TABLE reminder');
        $this->addSql('DROP TABLE "user"');
    }
}
