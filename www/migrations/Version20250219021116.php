<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250219021116 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mass_fetch_iteration ADD mass_fetch_job_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mass_fetch_iteration ADD CONSTRAINT FK_9F76B36E80597BF3 FOREIGN KEY (mass_fetch_job_id) REFERENCES mass_fetch_job (id)');
        $this->addSql('CREATE INDEX IDX_9F76B36E80597BF3 ON mass_fetch_iteration (mass_fetch_job_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mass_fetch_iteration DROP FOREIGN KEY FK_9F76B36E80597BF3');
        $this->addSql('DROP INDEX IDX_9F76B36E80597BF3 ON mass_fetch_iteration');
        $this->addSql('ALTER TABLE mass_fetch_iteration DROP mass_fetch_job_id');
    }
}
