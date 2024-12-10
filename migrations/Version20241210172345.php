<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241210CreateUserFollowersTable extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Crea la tabla user_followers para gestionar relaciones de seguidores entre usuarios.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE user_followers (
            user_id INT NOT NULL,
            follower_id INT NOT NULL,
            PRIMARY KEY (user_id, follower_id),
            CONSTRAINT FK_USER_FOLLOWERS_USER FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE,
            CONSTRAINT FK_USER_FOLLOWERS_FOLLOWER FOREIGN KEY (follower_id) REFERENCES user (id) ON DELETE CASCADE
        )');
        $this->addSql('CREATE INDEX IDX_USER_ID ON user_followers (user_id)');
        $this->addSql('CREATE INDEX IDX_FOLLOWER_ID ON user_followers (follower_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE user_followers');
    }
}

