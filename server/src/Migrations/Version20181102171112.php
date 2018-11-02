<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181102171112 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE member (militant_id INT NOT NULL, group_id INT NOT NULL, member ENUM(\'participant\', \'referent\'), inscription DATE NOT NULL, INDEX IDX_70E4FA782816464B (militant_id), INDEX IDX_70E4FA78FE54D947 (group_id), PRIMARY KEY(militant_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA782816464B FOREIGN KEY (militant_id) REFERENCES militant (id)');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA78FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id)');
        $this->addSql('DROP TABLE role');
        $this->addSql('ALTER TABLE participant ADD member ENUM(\'participant\', \'referent\', \'peacekeeper\'), DROP role');
        $this->addSql('ALTER TABLE event CHANGE type type ENUM(\'meeting\', \'protest\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE role (militant_id INT NOT NULL, group_id INT NOT NULL, role VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, inscription DATE NOT NULL, INDEX IDX_57698A6A2816464B (militant_id), INDEX IDX_57698A6AFE54D947 (group_id), PRIMARY KEY(militant_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6A2816464B FOREIGN KEY (militant_id) REFERENCES militant (id)');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6AFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id)');
        $this->addSql('DROP TABLE member');
        $this->addSql('ALTER TABLE event CHANGE type type VARCHAR(200) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE participant ADD role VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, DROP member');
    }
}
