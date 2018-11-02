<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181102160620 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE participant (militant_id INT NOT NULL, event_id INT NOT NULL, role ENUM(\'participant\', \'referent\', \'peacekeeper\'), inscription DATE NOT NULL, INDEX IDX_D79F6B112816464B (militant_id), INDEX IDX_D79F6B1171F7E88B (event_id), PRIMARY KEY(militant_id, event_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B112816464B FOREIGN KEY (militant_id) REFERENCES militant (id)');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B1171F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE role CHANGE role role ENUM(\'participant\', \'referent\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE participant');
        $this->addSql('ALTER TABLE role CHANGE role role VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
