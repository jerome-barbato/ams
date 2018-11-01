<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181101154412 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE role (militant_id INT NOT NULL, group_id INT NOT NULL, role VARCHAR(200) NOT NULL, INDEX IDX_57698A6A2816464B (militant_id), INDEX IDX_57698A6AFE54D947 (group_id), PRIMARY KEY(militant_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6A2816464B FOREIGN KEY (militant_id) REFERENCES militant (id)');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6AFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id)');
        $this->addSql('DROP TABLE militants_groups');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE militants_groups (militant_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_A528DA222816464B (militant_id), INDEX IDX_A528DA22FE54D947 (group_id), PRIMARY KEY(militant_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE militants_groups ADD CONSTRAINT FK_A528DA222816464B FOREIGN KEY (militant_id) REFERENCES militant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE militants_groups ADD CONSTRAINT FK_A528DA22FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE role');
    }
}
