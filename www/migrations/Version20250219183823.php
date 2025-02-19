<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250219183823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE channel_data (id INT AUTO_INCREMENT NOT NULL, channel_id INT NOT NULL, fetched_date DATETIME NOT NULL, videos_count INT NOT NULL, INDEX IDX_31A7501D72F5A1AA (channel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE channel_data ADD CONSTRAINT FK_31A7501D72F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (id)');
        $this->addSql('ALTER TABLE mass_fetch_iteration CHANGE time time DATETIME(6) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE channel_data DROP FOREIGN KEY FK_31A7501D72F5A1AA');
        $this->addSql('DROP TABLE channel_data');
        $this->addSql('ALTER TABLE mass_fetch_iteration CHANGE time time DATETIME NOT NULL');
    }
}
