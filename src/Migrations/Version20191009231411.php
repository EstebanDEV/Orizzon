<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191009231411 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE activities_domains DROP activity');
        $this->addSql('ALTER TABLE societies ADD activity_id INT NOT NULL');
        $this->addSql('ALTER TABLE societies ADD CONSTRAINT FK_9046D17E81C06096 FOREIGN KEY (activity_id) REFERENCES activities_domains (id)');
        $this->addSql('CREATE INDEX IDX_9046D17E81C06096 ON societies (activity_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE activities_domains ADD activity VARCHAR(100) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE societies DROP FOREIGN KEY FK_9046D17E81C06096');
        $this->addSql('DROP INDEX IDX_9046D17E81C06096 ON societies');
        $this->addSql('ALTER TABLE societies DROP activity_id');
    }
}
