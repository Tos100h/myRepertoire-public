<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230926140047 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'init database';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rep_artist (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, original_name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rep_media (id INT AUTO_INCREMENT NOT NULL, song_id INT NOT NULL, name VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, rank INT NOT NULL, INDEX IDX_1DE96D3AA0BDB2F3 (song_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rep_playlist (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, public TINYINT(1) NOT NULL, INDEX IDX_7B252605A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playlist_song (playlist_id INT NOT NULL, song_id INT NOT NULL, INDEX IDX_93F4D9C36BBD148 (playlist_id), INDEX IDX_93F4D9C3A0BDB2F3 (song_id), PRIMARY KEY(playlist_id, song_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rep_song (id INT AUTO_INCREMENT NOT NULL, create_by_id INT NOT NULL, name VARCHAR(255) NOT NULL, ost VARCHAR(255) DEFAULT NULL, creation_date DATETIME NOT NULL, original_name VARCHAR(255) DEFAULT NULL, INDEX IDX_F1114EA09E085865 (create_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE song_artist (song_id INT NOT NULL, artist_id INT NOT NULL, INDEX IDX_722870DA0BDB2F3 (song_id), INDEX IDX_722870DB7970CF8 (artist_id), PRIMARY KEY(song_id, artist_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE song_tag (song_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_4C49C104A0BDB2F3 (song_id), INDEX IDX_4C49C104BAD26311 (tag_id), PRIMARY KEY(song_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rep_tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rep_user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_4F6F7648E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rep_media ADD CONSTRAINT FK_1DE96D3AA0BDB2F3 FOREIGN KEY (song_id) REFERENCES rep_song (id)');
        $this->addSql('ALTER TABLE rep_playlist ADD CONSTRAINT FK_7B252605A76ED395 FOREIGN KEY (user_id) REFERENCES rep_user (id)');
        $this->addSql('ALTER TABLE playlist_song ADD CONSTRAINT FK_93F4D9C36BBD148 FOREIGN KEY (playlist_id) REFERENCES rep_playlist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE playlist_song ADD CONSTRAINT FK_93F4D9C3A0BDB2F3 FOREIGN KEY (song_id) REFERENCES rep_song (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rep_song ADD CONSTRAINT FK_F1114EA09E085865 FOREIGN KEY (create_by_id) REFERENCES rep_user (id)');
        $this->addSql('ALTER TABLE song_artist ADD CONSTRAINT FK_722870DA0BDB2F3 FOREIGN KEY (song_id) REFERENCES rep_song (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE song_artist ADD CONSTRAINT FK_722870DB7970CF8 FOREIGN KEY (artist_id) REFERENCES rep_artist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE song_tag ADD CONSTRAINT FK_4C49C104A0BDB2F3 FOREIGN KEY (song_id) REFERENCES rep_song (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE song_tag ADD CONSTRAINT FK_4C49C104BAD26311 FOREIGN KEY (tag_id) REFERENCES rep_tag (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rep_media DROP FOREIGN KEY FK_1DE96D3AA0BDB2F3');
        $this->addSql('ALTER TABLE rep_playlist DROP FOREIGN KEY FK_7B252605A76ED395');
        $this->addSql('ALTER TABLE playlist_song DROP FOREIGN KEY FK_93F4D9C36BBD148');
        $this->addSql('ALTER TABLE playlist_song DROP FOREIGN KEY FK_93F4D9C3A0BDB2F3');
        $this->addSql('ALTER TABLE rep_song DROP FOREIGN KEY FK_F1114EA09E085865');
        $this->addSql('ALTER TABLE song_artist DROP FOREIGN KEY FK_722870DA0BDB2F3');
        $this->addSql('ALTER TABLE song_artist DROP FOREIGN KEY FK_722870DB7970CF8');
        $this->addSql('ALTER TABLE song_tag DROP FOREIGN KEY FK_4C49C104A0BDB2F3');
        $this->addSql('ALTER TABLE song_tag DROP FOREIGN KEY FK_4C49C104BAD26311');
        $this->addSql('DROP TABLE rep_artist');
        $this->addSql('DROP TABLE rep_media');
        $this->addSql('DROP TABLE rep_playlist');
        $this->addSql('DROP TABLE playlist_song');
        $this->addSql('DROP TABLE rep_song');
        $this->addSql('DROP TABLE song_artist');
        $this->addSql('DROP TABLE song_tag');
        $this->addSql('DROP TABLE rep_tag');
        $this->addSql('DROP TABLE rep_user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
