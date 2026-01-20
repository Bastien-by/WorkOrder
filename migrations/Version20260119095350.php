<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260119095350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE work_order DROP piece_issued, DROP piece_type, DROP piece_brand, DROP sap_reference, DROP changed_elec_plan, DROP elec_plan, DROP elec_plan_picture, CHANGE additional_details intervention_description LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE work_order ADD piece_issued TINYINT(1) DEFAULT NULL, ADD piece_type VARCHAR(30) DEFAULT NULL, ADD piece_brand VARCHAR(30) DEFAULT NULL, ADD sap_reference VARCHAR(30) DEFAULT NULL, ADD changed_elec_plan TINYINT(1) DEFAULT NULL, ADD elec_plan TINYINT(1) DEFAULT NULL, ADD elec_plan_picture VARCHAR(255) DEFAULT NULL, CHANGE intervention_description additional_details LONGTEXT DEFAULT NULL');
    }
}
