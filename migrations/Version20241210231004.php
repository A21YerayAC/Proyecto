<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241210231004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Si no necesitas hacer cambios en review, puedes omitir este bloque
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6A76ED395');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');

        // Crear las columnas followers y following como JSON
        $this->addSql('ALTER TABLE user ADD followers JSON NOT NULL, ADD following JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // Eliminar las columnas followers y following
        $this->addSql('ALTER TABLE user DROP followers, DROP following');

        // Restaurar la clave foránea en review (si es necesario)
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6A76ED395');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}

