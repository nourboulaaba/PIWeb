<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création de la table notes pour les avis sur les formations';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notes (
            id INT AUTO_INCREMENT NOT NULL,
            formation_id INT NOT NULL,
            user_id INT NOT NULL,
            valeur INT NOT NULL,
            commentaire TEXT DEFAULT NULL,
            date_creation DATETIME NOT NULL,
            INDEX IDX_11BA68C5200282E (formation_id),
            INDEX IDX_11BA68CA76ED395 (user_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        
        $this->addSql('ALTER TABLE notes ADD CONSTRAINT FK_11BA68C5200282E FOREIGN KEY (formation_id) REFERENCES formations (id)');
        $this->addSql('ALTER TABLE notes ADD CONSTRAINT FK_11BA68CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notes DROP FOREIGN KEY FK_11BA68C5200282E');
        $this->addSql('ALTER TABLE notes DROP FOREIGN KEY FK_11BA68CA76ED395');
        $this->addSql('DROP TABLE notes');
    }
}
