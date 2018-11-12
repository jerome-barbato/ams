<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181104160054 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE auth_token (id INT AUTO_INCREMENT NOT NULL, militant_id INT DEFAULT NULL, value VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_9315F04E1D775834 (value), INDEX IDX_9315F04E2816464B (militant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE auth_token ADD CONSTRAINT FK_9315F04E2816464B FOREIGN KEY (militant_id) REFERENCES militant (id)');
        $this->addSql('ALTER TABLE participant CHANGE role role ENUM(\'participant\', \'referent\', \'peacekeeper\')');
        $this->addSql('ALTER TABLE event CHANGE type type ENUM(\'meeting\', \'protest\')');
        $this->addSql('ALTER TABLE member CHANGE role role ENUM(\'participant\', \'referent\')');
        $this->addSql('DROP INDEX UNIQ_DAA8DCF87BA2F5EB ON militant');
        $this->addSql('ALTER TABLE militant DROP api_token');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE auth_token');
        $this->addSql('ALTER TABLE event CHANGE type type VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE member CHANGE role role VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE militant ADD api_token VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DAA8DCF87BA2F5EB ON militant (api_token)');
        $this->addSql('ALTER TABLE participant CHANGE role role VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
