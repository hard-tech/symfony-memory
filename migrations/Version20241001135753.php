<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241001135753 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE card_games (card_id INT NOT NULL, games_id INT NOT NULL, INDEX IDX_C717DAF74ACC9A20 (card_id), INDEX IDX_C717DAF797FFC673 (games_id), PRIMARY KEY(card_id, games_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE games (id INT AUTO_INCREMENT NOT NULL, difficulty INT NOT NULL, score INT NOT NULL, end_game TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_games (user_id INT NOT NULL, games_id INT NOT NULL, INDEX IDX_1DE1D069A76ED395 (user_id), INDEX IDX_1DE1D06997FFC673 (games_id), PRIMARY KEY(user_id, games_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE card_games ADD CONSTRAINT FK_C717DAF74ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE card_games ADD CONSTRAINT FK_C717DAF797FFC673 FOREIGN KEY (games_id) REFERENCES games (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_games ADD CONSTRAINT FK_1DE1D069A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_games ADD CONSTRAINT FK_1DE1D06997FFC673 FOREIGN KEY (games_id) REFERENCES games (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE user_stats');
        $this->addSql('DROP TABLE party');
        $this->addSql('ALTER TABLE card ADD theme_id INT NOT NULL');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D359027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('CREATE INDEX IDX_161498D359027487 ON card (theme_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_stats (id INT AUTO_INCREMENT NOT NULL, nb_reversed_card INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE party (id INT AUTO_INCREMENT NOT NULL, difficulty INT NOT NULL, score INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE card_games DROP FOREIGN KEY FK_C717DAF74ACC9A20');
        $this->addSql('ALTER TABLE card_games DROP FOREIGN KEY FK_C717DAF797FFC673');
        $this->addSql('ALTER TABLE user_games DROP FOREIGN KEY FK_1DE1D069A76ED395');
        $this->addSql('ALTER TABLE user_games DROP FOREIGN KEY FK_1DE1D06997FFC673');
        $this->addSql('DROP TABLE card_games');
        $this->addSql('DROP TABLE games');
        $this->addSql('DROP TABLE user_games');
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D359027487');
        $this->addSql('DROP INDEX IDX_161498D359027487 ON card');
        $this->addSql('ALTER TABLE card DROP theme_id');
    }
}
