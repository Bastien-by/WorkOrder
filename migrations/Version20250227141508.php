<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250227141508 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE work_order CHANGE downtime_start_time downtime_start_time TIME DEFAULT NULL COMMENT \'(DC2Type:time_immutable)\', CHANGE downtime_end_time downtime_end_time TIME DEFAULT NULL COMMENT \'(DC2Type:time_immutable)\', CHANGE intervention_start_time intervention_start_time TIME DEFAULT NULL COMMENT \'(DC2Type:time_immutable)\', CHANGE intervention_end_time intervention_end_time TIME DEFAULT NULL COMMENT \'(DC2Type:time_immutable)\', CHANGE intervention_resquester intervention_requester VARCHAR(30) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE work_order CHANGE downtime_start_time downtime_start_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE downtime_end_time downtime_end_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE intervention_start_time intervention_start_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE intervention_end_time intervention_end_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE intervention_requester intervention_resquester VARCHAR(30) NOT NULL');
    }
}
