<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250429231041 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE application DROP FOREIGN KEY FK_application_recrutement
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE application DROP FOREIGN KEY FK_application_user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE application DROP FOREIGN KEY FK_application_recrutement
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE application DROP FOREIGN KEY FK_application_user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE application ADD CONSTRAINT FK_A45BDDC1FCC7117B FOREIGN KEY (recrutement_id) REFERENCES recrutement (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE application ADD CONSTRAINT FK_A45BDDC1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_application_recrutement ON application
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_A45BDDC1FCC7117B ON application (recrutement_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_application_user ON application
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_A45BDDC1A76ED395 ON application (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE application ADD CONSTRAINT FK_application_recrutement FOREIGN KEY (recrutement_id) REFERENCES recrutement (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE application ADD CONSTRAINT FK_application_user FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE entretien CHANGE id id INT AUTO_INCREMENT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE entretien ADD CONSTRAINT FK_2B58D6DAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2B58D6DAA76ED395 ON entretien (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recrutement ADD CONSTRAINT FK_25EB23194CC8505A FOREIGN KEY (offre_id) REFERENCES offre (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_25EB23194CC8505A ON recrutement (offre_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE id id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE available_at available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC1FCC7117B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC1A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC1FCC7117B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC1A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE application ADD CONSTRAINT FK_application_recrutement FOREIGN KEY (recrutement_id) REFERENCES recrutement (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE application ADD CONSTRAINT FK_application_user FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_a45bddc1fcc7117b ON application
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX FK_application_recrutement ON application (recrutement_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_a45bddc1a76ed395 ON application
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX FK_application_user ON application (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE application ADD CONSTRAINT FK_A45BDDC1FCC7117B FOREIGN KEY (recrutement_id) REFERENCES recrutement (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE application ADD CONSTRAINT FK_A45BDDC1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE entretien DROP FOREIGN KEY FK_2B58D6DAA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_2B58D6DAA76ED395 ON entretien
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE entretien CHANGE id id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL, CHANGE available_at available_at DATETIME NOT NULL, CHANGE delivered_at delivered_at DATETIME DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recrutement DROP FOREIGN KEY FK_25EB23194CC8505A
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_25EB23194CC8505A ON recrutement
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE id id INT AUTO_INCREMENT NOT NULL
        SQL);
    }
}
