<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250901133949 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE work_order CHANGE downtime_start_time downtime_start_time DATETIME DEFAULT NULL, CHANGE downtime_end_time downtime_end_time DATETIME DEFAULT NULL, CHANGE intervention_start_time intervention_start_time DATETIME DEFAULT NULL, CHANGE intervention_end_time intervention_end_time DATETIME DEFAULT NULL, CHANGE downtime_time downtime_time DATETIME DEFAULT NULL, CHANGE intervention_time intervention_time DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE work_order CHANGE downtime_start_time downtime_start_time TIME DEFAULT NULL, CHANGE downtime_end_time downtime_end_time TIME DEFAULT NULL, CHANGE downtime_time downtime_time TIME DEFAULT NULL, CHANGE intervention_start_time intervention_start_time TIME DEFAULT NULL, CHANGE intervention_end_time intervention_end_time TIME DEFAULT NULL, CHANGE intervention_time intervention_time TIME DEFAULT NULL');
    }
}
