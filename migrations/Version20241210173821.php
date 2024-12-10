<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241210173821 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_followers DROP FOREIGN KEY FK_USER_FOLLOWERS_FOLLOWER');
        $this->addSql('ALTER TABLE user_followers DROP FOREIGN KEY FK_USER_FOLLOWERS_USER');
        $this->addSql('DROP TABLE user_followers');
        $this->addSql('ALTER TABLE user ADD imagen_perfil_ruta VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_followers (user_id INT NOT NULL, follower_id INT NOT NULL, INDEX IDX_FOLLOWER_ID (follower_id), INDEX IDX_USER_ID (user_id), PRIMARY KEY(user_id, follower_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_followers ADD CONSTRAINT FK_USER_FOLLOWERS_FOLLOWER FOREIGN KEY (follower_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_followers ADD CONSTRAINT FK_USER_FOLLOWERS_USER FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user DROP imagen_perfil_ruta');
    }
}
