<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181102173824 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE participant ADD role ENUM(\'participant\', \'referent\', \'peacekeeper\'), DROP member');
        $this->addSql('ALTER TABLE event CHANGE type type ENUM(\'meeting\', \'protest\')');
        $this->addSql('ALTER TABLE member ADD role ENUM(\'participant\', \'referent\'), DROP member');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE event CHANGE type type VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE member ADD member VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, DROP role');
        $this->addSql('ALTER TABLE participant ADD member VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, DROP role');
    }
}
