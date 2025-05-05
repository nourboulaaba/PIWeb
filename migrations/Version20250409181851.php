<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250409181851 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE entretien CHANGE id id INT NOT NULL, CHANGE approved approved TINYINT(1) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE entretien ADD CONSTRAINT FK_2B58D6DAFCC7117B FOREIGN KEY (recrutement_id) REFERENCES recrutement (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2B58D6DAFCC7117B ON entretien (recrutement_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offre CHANGE id id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recrutement ADD date_fin DATE NOT NULL, DROP dateFin, CHANGE id id INT NOT NULL, CHANGE dateDebut date_debut DATE NOT NULL, CHANGE NbEntretien nb_entretien INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recrutement ADD CONSTRAINT FK_25EB23194CC8505A FOREIGN KEY (offre_id) REFERENCES offre (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_25EB23194CC8505A ON recrutement (offre_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE id id INT NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE entretien DROP FOREIGN KEY FK_2B58D6DAFCC7117B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_2B58D6DAFCC7117B ON entretien
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE entretien CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE approved approved TINYINT(1) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offre CHANGE id id INT AUTO_INCREMENT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recrutement DROP FOREIGN KEY FK_25EB23194CC8505A
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_25EB23194CC8505A ON recrutement
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recrutement ADD dateDebut DATE NOT NULL, ADD dateFin DATE DEFAULT NULL, DROP date_debut, DROP date_fin, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE nb_entretien NbEntretien INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE id id INT AUTO_INCREMENT NOT NULL
        SQL);
    }
}
