<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181102155650 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, place_id INT DEFAULT NULL, uuid VARCHAR(13) NOT NULL, title VARCHAR(200) NOT NULL, description LONGTEXT DEFAULT NULL, creation DATE NOT NULL, begin DATE DEFAULT NULL, end DATE DEFAULT NULL, type VARCHAR(200) DEFAULT NULL, image VARCHAR(200) DEFAULT NULL, INDEX IDX_3BAE0AA7DA6A219 (place_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(200) DEFAULT NULL, address VARCHAR(200) NOT NULL, postal_code VARCHAR(6) NOT NULL, city VARCHAR(200) NOT NULL, country VARCHAR(200) NOT NULL, lat DOUBLE PRECISION NOT NULL, lng DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7DA6A219 FOREIGN KEY (place_id) REFERENCES place (id)');
        $this->addSql('ALTER TABLE role CHANGE role role ENUM(\'participant\', \'referent\')');
        $this->addSql('ALTER TABLE militant ADD place_id INT DEFAULT NULL, DROP lat, DROP lng, DROP address, DROP postal_code, DROP city, DROP country');
        $this->addSql('ALTER TABLE militant ADD CONSTRAINT FK_DAA8DCF8DA6A219 FOREIGN KEY (place_id) REFERENCES place (id)');
        $this->addSql('CREATE INDEX IDX_DAA8DCF8DA6A219 ON militant (place_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7DA6A219');
        $this->addSql('ALTER TABLE militant DROP FOREIGN KEY FK_DAA8DCF8DA6A219');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE place');
        $this->addSql('DROP INDEX IDX_DAA8DCF8DA6A219 ON militant');
        $this->addSql('ALTER TABLE militant ADD lat DOUBLE PRECISION DEFAULT NULL, ADD lng DOUBLE PRECISION DEFAULT NULL, ADD address VARCHAR(200) NOT NULL COLLATE utf8mb4_unicode_ci, ADD postal_code VARCHAR(6) NOT NULL COLLATE utf8mb4_unicode_ci, ADD city VARCHAR(200) NOT NULL COLLATE utf8mb4_unicode_ci, ADD country VARCHAR(200) NOT NULL COLLATE utf8mb4_unicode_ci, DROP place_id');
        $this->addSql('ALTER TABLE role CHANGE role role VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
