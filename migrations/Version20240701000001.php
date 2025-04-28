<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240701000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute la colonne related_user_id à la table notification';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notification ADD related_user_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notification DROP related_user_id');
    }
}
