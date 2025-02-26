<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250226110129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE work_order (id INT AUTO_INCREMENT NOT NULL, technician_name VARCHAR(50) NOT NULL, maitenance_type VARCHAR(30) NOT NULL, intervention_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', machine_name VARCHAR(30) NOT NULL, technical_position VARCHAR(30) DEFAULT NULL, downtime_start_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', downtime_end_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', intervention_start_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', intervention_end_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', field_intervention VARCHAR(30) NOT NULL, intervention_resquester VARCHAR(30) NOT NULL, intervention_request_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', technical_details LONGTEXT DEFAULT NULL, piece_issued TINYINT(1) NOT NULL, piece_type VARCHAR(30) NOT NULL, piece_brand VARCHAR(30) NOT NULL, sap_reference VARCHAR(30) DEFAULT NULL, quantity INT DEFAULT NULL, brand VARCHAR(30) DEFAULT NULL, type VARCHAR(30) NOT NULL, size VARCHAR(50) NOT NULL, manufacturer_reference VARCHAR(30) NOT NULL, created_piece_quantity INT NOT NULL, additional_details LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE maintenance_participant DROP FOREIGN KEY FK_DDB882B6F6C202BC');
        $this->addSql('ALTER TABLE maintenance_participant DROP FOREIGN KEY FK_DDB882B69D1C3019');
        $this->addSql('ALTER TABLE maintenance DROP FOREIGN KEY FK_2F84F8E9F6B75B26');
        $this->addSql('DROP TABLE downtime_intervention_duration');
        $this->addSql('DROP TABLE intervention_description');
        $this->addSql('DROP TABLE machine');
        $this->addSql('DROP TABLE participant');
        $this->addSql('DROP TABLE parts_to_create');
        $this->addSql('DROP TABLE store_management');
        $this->addSql('DROP TABLE maintenance_participant');
        $this->addSql('DROP TABLE maintenance');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE downtime_intervention_duration (id INT AUTO_INCREMENT NOT NULL, downtime_start_time DATETIME NOT NULL, intervention_start_time DATETIME NOT NULL, downtime_end_time DATETIME NOT NULL, intervention_end_time DATETIME NOT NULL, downtime_duration DOUBLE PRECISION NOT NULL, intervention_duration DOUBLE PRECISION NOT NULL, intervention_field VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE intervention_description (id INT AUTO_INCREMENT NOT NULL, long_description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, part_photo VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE machine (id INT AUTO_INCREMENT NOT NULL, machine_name VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, sector VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE participant (id INT AUTO_INCREMENT NOT NULL, participant_name VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, technical_position VARCHAR(30) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE parts_to_create (id INT AUTO_INCREMENT NOT NULL, brand VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, part_type VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, manufacturer_reference VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, dimension VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, quantity INT NOT NULL, additional_description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, part_to_create_photo VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE store_management (id INT AUTO_INCREMENT NOT NULL, part_outflow TINYINT(1) NOT NULL, part_type VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, brand_manufacturer VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, sap_part_reference VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE maintenance_participant (maintenance_id INT NOT NULL, participant_id INT NOT NULL, INDEX IDX_DDB882B6F6C202BC (maintenance_id), INDEX IDX_DDB882B69D1C3019 (participant_id), PRIMARY KEY(maintenance_id, participant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE maintenance (id INT AUTO_INCREMENT NOT NULL, machine_id INT NOT NULL, maintenance_type VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, intervention_request_date DATETIME NOT NULL, intervention_requester VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_2F84F8E9F6B75B26 (machine_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE maintenance_participant ADD CONSTRAINT FK_DDB882B6F6C202BC FOREIGN KEY (maintenance_id) REFERENCES maintenance (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE maintenance_participant ADD CONSTRAINT FK_DDB882B69D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE maintenance ADD CONSTRAINT FK_2F84F8E9F6B75B26 FOREIGN KEY (machine_id) REFERENCES machine (id)');
        $this->addSql('DROP TABLE work_order');
    }
}
