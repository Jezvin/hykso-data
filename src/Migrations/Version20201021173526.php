<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201021173526 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE coach (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE punch (id INT AUTO_INCREMENT NOT NULL, round_id INT NOT NULL, time DOUBLE PRECISION NOT NULL, hand VARCHAR(10) NOT NULL, type VARCHAR(10) NOT NULL, velocity DOUBLE PRECISION NOT NULL, intensity DOUBLE PRECISION NOT NULL, INDEX IDX_2CE17058A6005CA0 (round_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE round (id INT AUTO_INCREMENT NOT NULL, session_id INT DEFAULT NULL, duration DOUBLE PRECISION DEFAULT NULL, number INT NOT NULL, INDEX IDX_C5EEEA34613FECDF (session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session (id INT AUTO_INCREMENT NOT NULL, sport_id INT DEFAULT NULL, date DATETIME NOT NULL, duration DOUBLE PRECISION NOT NULL, name VARCHAR(100) NOT NULL, description VARCHAR(100) NOT NULL, INDEX IDX_D044D5D4AC78BCF8 (sport_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session_coach (session_id INT NOT NULL, coach_id INT NOT NULL, INDEX IDX_E424D51A613FECDF (session_id), INDEX IDX_E424D51A3C105691 (coach_id), PRIMARY KEY(session_id, coach_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sport (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, initials VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE punch ADD CONSTRAINT FK_2CE17058A6005CA0 FOREIGN KEY (round_id) REFERENCES round (id)');
        $this->addSql('ALTER TABLE round ADD CONSTRAINT FK_C5EEEA34613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4AC78BCF8 FOREIGN KEY (sport_id) REFERENCES sport (id)');
        $this->addSql('ALTER TABLE session_coach ADD CONSTRAINT FK_E424D51A613FECDF FOREIGN KEY (session_id) REFERENCES session (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session_coach ADD CONSTRAINT FK_E424D51A3C105691 FOREIGN KEY (coach_id) REFERENCES coach (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE session_coach DROP FOREIGN KEY FK_E424D51A3C105691');
        $this->addSql('ALTER TABLE punch DROP FOREIGN KEY FK_2CE17058A6005CA0');
        $this->addSql('ALTER TABLE round DROP FOREIGN KEY FK_C5EEEA34613FECDF');
        $this->addSql('ALTER TABLE session_coach DROP FOREIGN KEY FK_E424D51A613FECDF');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4AC78BCF8');
        $this->addSql('DROP TABLE coach');
        $this->addSql('DROP TABLE punch');
        $this->addSql('DROP TABLE round');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP TABLE session_coach');
        $this->addSql('DROP TABLE sport');
    }


    public function postUp(Schema $schema): void
    {
        $this->connection->insert('coach', ['name' => "Lily"]);
        $this->connection->insert('coach', ['name' => "Coco"]);
        $this->connection->insert('coach', ['name' => "Yovan"]);
        $this->connection->insert('coach', ['name' => "Arnaud"]);

        $this->connection->insert('sport', ['name' => "Strike", "initials" => "S"]);
        $this->connection->insert('sport', ['name' => "Speed Bag", "initials" => "SB"]);
        $this->connection->insert('sport', ['name' => "MMA", "initials" => "M"]);
        $this->connection->insert('sport', ['name' => "Bag", "initials" => "B"]);
        $this->connection->insert('sport', ['name' => "Warm Up", "initials" => "W"]);
        $this->connection->insert('sport', ['name' => "Shadow", "initials" => "Shadow"]);
        $this->connection->insert('sport', ['name' => "Sparring", "initials" => "Sparring"]);
        $this->connection->insert('sport', ['name' => "Dynamic Strike", "initials" => "Ds"]);

    }
}
