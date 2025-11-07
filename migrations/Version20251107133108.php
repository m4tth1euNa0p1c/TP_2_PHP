<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251107133108 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE driver (id INT AUTO_INCREMENT NOT NULL, team_id INT DEFAULT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, is_starter TINYINT(1) NOT NULL, license_points INT NOT NULL, status VARCHAR(20) NOT NULL, f1_start_date DATE NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_11667CD9296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE engine (id INT AUTO_INCREMENT NOT NULL, team_id INT NOT NULL, brand VARCHAR(120) NOT NULL, UNIQUE INDEX UNIQ_E8A81A8D296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE infraction (id INT AUTO_INCREMENT NOT NULL, driver_id INT DEFAULT NULL, team_id INT DEFAULT NULL, occurred_at DATETIME NOT NULL, race_name VARCHAR(160) NOT NULL, description LONGTEXT NOT NULL, type VARCHAR(20) NOT NULL, amount NUMERIC(12, 2) NOT NULL, INDEX IDX_C1A458F5C3423909 (driver_id), INDEX IDX_C1A458F5296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_C4E0A61F5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE driver ADD CONSTRAINT FK_11667CD9296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE engine ADD CONSTRAINT FK_E8A81A8D296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE infraction ADD CONSTRAINT FK_C1A458F5C3423909 FOREIGN KEY (driver_id) REFERENCES driver (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE infraction ADD CONSTRAINT FK_C1A458F5296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE driver DROP FOREIGN KEY FK_11667CD9296CD8AE');
        $this->addSql('ALTER TABLE engine DROP FOREIGN KEY FK_E8A81A8D296CD8AE');
        $this->addSql('ALTER TABLE infraction DROP FOREIGN KEY FK_C1A458F5C3423909');
        $this->addSql('ALTER TABLE infraction DROP FOREIGN KEY FK_C1A458F5296CD8AE');
        $this->addSql('DROP TABLE driver');
        $this->addSql('DROP TABLE engine');
        $this->addSql('DROP TABLE infraction');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE `user`');
    }
}
