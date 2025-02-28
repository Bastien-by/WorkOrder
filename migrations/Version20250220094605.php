<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250220094605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE maintenance_participant (maintenance_id INT NOT NULL, participant_id INT NOT NULL, INDEX IDX_DDB882B6F6C202BC (maintenance_id), INDEX IDX_DDB882B69D1C3019 (participant_id), PRIMARY KEY(maintenance_id, participant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE maintenance_participant ADD CONSTRAINT FK_DDB882B6F6C202BC FOREIGN KEY (maintenance_id) REFERENCES maintenance (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE maintenance_participant ADD CONSTRAINT FK_DDB882B69D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE maintenance ADD machine_id INT NOT NULL');
        $this->addSql('ALTER TABLE maintenance ADD CONSTRAINT FK_2F84F8E9F6B75B26 FOREIGN KEY (machine_id) REFERENCES machine (id)');
        $this->addSql('CREATE INDEX IDX_2F84F8E9F6B75B26 ON maintenance (machine_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE maintenance_participant DROP FOREIGN KEY FK_DDB882B6F6C202BC');
        $this->addSql('ALTER TABLE maintenance_participant DROP FOREIGN KEY FK_DDB882B69D1C3019');
        $this->addSql('DROP TABLE maintenance_participant');
        $this->addSql('ALTER TABLE maintenance DROP FOREIGN KEY FK_2F84F8E9F6B75B26');
        $this->addSql('DROP INDEX IDX_2F84F8E9F6B75B26 ON maintenance');
        $this->addSql('ALTER TABLE maintenance DROP machine_id');
    }
}
