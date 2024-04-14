<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240413123725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE post_user_likes (user_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_8870BCE4A76ED395 (user_id), INDEX IDX_8870BCE44B89032C (post_id), PRIMARY KEY(user_id, post_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_user_dislikes (user_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_D20FE402A76ED395 (user_id), INDEX IDX_D20FE4024B89032C (post_id), PRIMARY KEY(user_id, post_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE post_user_likes ADD CONSTRAINT FK_8870BCE4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_user_likes ADD CONSTRAINT FK_8870BCE44B89032C FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_user_dislikes ADD CONSTRAINT FK_D20FE402A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_user_dislikes ADD CONSTRAINT FK_D20FE4024B89032C FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post_user_likes DROP FOREIGN KEY FK_8870BCE4A76ED395');
        $this->addSql('ALTER TABLE post_user_likes DROP FOREIGN KEY FK_8870BCE44B89032C');
        $this->addSql('ALTER TABLE post_user_dislikes DROP FOREIGN KEY FK_D20FE402A76ED395');
        $this->addSql('ALTER TABLE post_user_dislikes DROP FOREIGN KEY FK_D20FE4024B89032C');
        $this->addSql('DROP TABLE post_user_likes');
        $this->addSql('DROP TABLE post_user_dislikes');
    }
}
