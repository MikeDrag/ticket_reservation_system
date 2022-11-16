<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221115193837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE airport (id INT AUTO_INCREMENT NOT NULL, iata VARCHAR(3) NOT NULL, icao VARCHAR(4) NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, from_airport_id INT DEFAULT NULL, to_airport_id INT DEFAULT NULL, passenger_seat INT DEFAULT NULL, passenger_passport_id INT NOT NULL, departure_time DATETIME DEFAULT NULL, INDEX IDX_97A0ADA333B95CF (from_airport_id), INDEX IDX_97A0ADA3FACB1B5 (to_airport_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA333B95CF FOREIGN KEY (from_airport_id) REFERENCES airport (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3FACB1B5 FOREIGN KEY (to_airport_id) REFERENCES airport (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA333B95CF');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3FACB1B5');
        $this->addSql('DROP TABLE airport');
        $this->addSql('DROP TABLE ticket');
    }
}
