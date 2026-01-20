<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260119095504 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE work_order DROP quantity, DROP brand, DROP type, DROP size, DROP manufacturer_reference, DROP created_piece_quantity');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE work_order ADD quantity INT DEFAULT NULL, ADD brand VARCHAR(30) DEFAULT NULL, ADD type VARCHAR(30) DEFAULT NULL, ADD size VARCHAR(50) DEFAULT NULL, ADD manufacturer_reference VARCHAR(30) DEFAULT NULL, ADD created_piece_quantity INT DEFAULT NULL');
    }
}
