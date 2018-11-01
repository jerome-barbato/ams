<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181101152002 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE `group` (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, image VARCHAR(200) DEFAULT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE $militants_groups (militant_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_DDD775FF2816464B (militant_id), INDEX IDX_DDD775FFFE54D947 (group_id), PRIMARY KEY(militant_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE $militants_groups ADD CONSTRAINT FK_DDD775FF2816464B FOREIGN KEY (militant_id) REFERENCES militant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE $militants_groups ADD CONSTRAINT FK_DDD775FFFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE $militants_groups DROP FOREIGN KEY FK_DDD775FFFE54D947');
        $this->addSql('DROP TABLE `group`');
        $this->addSql('DROP TABLE $militants_groups');
    }
}
