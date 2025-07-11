<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250711072058 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP CONSTRAINT FK_3BAE0AA77D182D95');
        $this->addSql('ALTER TABLE event ALTER created_by_user_id DROP NOT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA77D182D95 FOREIGN KEY (created_by_user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE participation DROP CONSTRAINT FK_AB55E24F9D1C3019');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24F9D1C3019 FOREIGN KEY (participant_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE participation DROP CONSTRAINT fk_ab55e24f9d1c3019');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT fk_ab55e24f9d1c3019 FOREIGN KEY (participant_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE event DROP CONSTRAINT fk_3bae0aa77d182d95');
        $this->addSql('ALTER TABLE event ALTER created_by_user_id SET NOT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT fk_3bae0aa77d182d95 FOREIGN KEY (created_by_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
