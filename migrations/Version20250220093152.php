<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250220093152 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE downtime_intervention_duration (id INT AUTO_INCREMENT NOT NULL, downtime_start_time DATETIME NOT NULL, intervention_start_time DATETIME NOT NULL, downtime_end_time DATETIME NOT NULL, intervention_end_time DATETIME NOT NULL, downtime_duration DOUBLE PRECISION NOT NULL, intervention_duration DOUBLE PRECISION NOT NULL, intervention_field VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE intervention_description (id INT AUTO_INCREMENT NOT NULL, long_description LONGTEXT NOT NULL, part_photo VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE machine (id INT AUTO_INCREMENT NOT NULL, machine_name VARCHAR(30) NOT NULL, sector VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE maintenance (id INT AUTO_INCREMENT NOT NULL, maintenance_type VARCHAR(30) NOT NULL, intervention_request_date DATETIME NOT NULL, intervention_requester VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participant (id INT AUTO_INCREMENT NOT NULL, participant_name VARCHAR(20) NOT NULL, technical_position VARCHAR(30) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parts_to_create (id INT AUTO_INCREMENT NOT NULL, brand VARCHAR(30) NOT NULL, part_type VARCHAR(30) NOT NULL, manufacturer_reference VARCHAR(255) NOT NULL, dimension VARCHAR(255) NOT NULL, quantity INT NOT NULL, additional_description LONGTEXT NOT NULL, part_to_create_photo VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE store_management (id INT AUTO_INCREMENT NOT NULL, part_outflow TINYINT(1) NOT NULL, part_type VARCHAR(30) NOT NULL, brand_manufacturer VARCHAR(30) NOT NULL, sap_part_reference VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE downtime_intervention_duration');
        $this->addSql('DROP TABLE intervention_description');
        $this->addSql('DROP TABLE machine');
        $this->addSql('DROP TABLE maintenance');
        $this->addSql('DROP TABLE participant');
        $this->addSql('DROP TABLE parts_to_create');
        $this->addSql('DROP TABLE store_management');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
