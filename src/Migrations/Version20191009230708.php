<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191009230708 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users ADD answer_id INT NOT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9AA334807 FOREIGN KEY (answer_id) REFERENCES informative_answers (id)');
        $this->addSql('CREATE INDEX IDX_1483A5E9AA334807 ON users (answer_id)');
        $this->addSql('ALTER TABLE societies ADD activity_id INT NOT NULL');
        $this->addSql('ALTER TABLE societies ADD CONSTRAINT FK_9046D17E81C06096 FOREIGN KEY (activity_id) REFERENCES activities_domains (id)');
        $this->addSql('CREATE INDEX IDX_9046D17E81C06096 ON societies (activity_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE societies DROP FOREIGN KEY FK_9046D17E81C06096');
        $this->addSql('DROP INDEX IDX_9046D17E81C06096 ON societies');
        $this->addSql('ALTER TABLE societies DROP activity_id');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9AA334807');
        $this->addSql('DROP INDEX IDX_1483A5E9AA334807 ON users');
        $this->addSql('ALTER TABLE users DROP answer_id');
    }
}
