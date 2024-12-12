<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241210232115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_followers (user_id INT NOT NULL, follower_id INT NOT NULL, INDEX IDX_84E87043A76ED395 (user_id), INDEX IDX_84E87043AC24F853 (follower_id), PRIMARY KEY(user_id, follower_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_followers ADD CONSTRAINT FK_84E87043A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_followers ADD CONSTRAINT FK_84E87043AC24F853 FOREIGN KEY (follower_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user DROP followers, DROP following');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_followers DROP FOREIGN KEY FK_84E87043A76ED395');
        $this->addSql('ALTER TABLE user_followers DROP FOREIGN KEY FK_84E87043AC24F853');
        $this->addSql('DROP TABLE user_followers');
        $this->addSql('ALTER TABLE user ADD followers JSON NOT NULL, ADD following JSON NOT NULL');
    }
}
