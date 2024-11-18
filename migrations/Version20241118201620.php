<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241118201620 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE review CHANGE fecha_publicacion fecha_publicacion DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user_followers ADD CONSTRAINT FK_84E870433AD8644E FOREIGN KEY (user_source) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_followers ADD CONSTRAINT FK_84E87043233D34C1 FOREIGN KEY (user_target) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_84E870433AD8644E ON user_followers (user_source)');
        $this->addSql('CREATE INDEX IDX_84E87043233D34C1 ON user_followers (user_target)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_followers DROP FOREIGN KEY FK_84E870433AD8644E');
        $this->addSql('ALTER TABLE user_followers DROP FOREIGN KEY FK_84E87043233D34C1');
        $this->addSql('DROP INDEX IDX_84E870433AD8644E ON user_followers');
        $this->addSql('DROP INDEX IDX_84E87043233D34C1 ON user_followers');
        $this->addSql('ALTER TABLE review CHANGE fecha_publicacion fecha_publicacion DATETIME NOT NULL');
    }
}
