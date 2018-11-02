<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181102181754 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE material_militant (material_id INT NOT NULL, militant_id INT NOT NULL, INDEX IDX_9C70F85AE308AC6F (material_id), INDEX IDX_9C70F85A2816464B (militant_id), PRIMARY KEY(material_id, militant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE material_militant ADD CONSTRAINT FK_9C70F85AE308AC6F FOREIGN KEY (material_id) REFERENCES material (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE material_militant ADD CONSTRAINT FK_9C70F85A2816464B FOREIGN KEY (militant_id) REFERENCES militant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participant CHANGE role role ENUM(\'participant\', \'referent\', \'peacekeeper\')');
        $this->addSql('ALTER TABLE event CHANGE type type ENUM(\'meeting\', \'protest\')');
        $this->addSql('ALTER TABLE member CHANGE role role ENUM(\'participant\', \'referent\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE material_militant');
        $this->addSql('ALTER TABLE event CHANGE type type VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE member CHANGE role role VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE participant CHANGE role role VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
