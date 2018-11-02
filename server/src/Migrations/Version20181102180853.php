<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181102180853 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE material (id INT AUTO_INCREMENT NOT NULL, place_id INT NOT NULL, uuid VARCHAR(13) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, type VARCHAR(255) NOT NULL, size VARCHAR(255) DEFAULT NULL, quantity INT NOT NULL, image VARCHAR(255) DEFAULT NULL, theme VARCHAR(255) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, INDEX IDX_7CBE7595DA6A219 (place_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE news (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, slug VARCHAR(200) NOT NULL, title VARCHAR(255) NOT NULL, text LONGTEXT DEFAULT NULL, creation DATE NOT NULL, updated DATE NOT NULL, image VARCHAR(200) DEFAULT NULL, INDEX IDX_1DD39950F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE news_group (news_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_177FC39BB5A459A0 (news_id), INDEX IDX_177FC39BFE54D947 (group_id), PRIMARY KEY(news_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE material ADD CONSTRAINT FK_7CBE7595DA6A219 FOREIGN KEY (place_id) REFERENCES place (id)');
        $this->addSql('ALTER TABLE news ADD CONSTRAINT FK_1DD39950F675F31B FOREIGN KEY (author_id) REFERENCES militant (id)');
        $this->addSql('ALTER TABLE news_group ADD CONSTRAINT FK_177FC39BB5A459A0 FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE news_group ADD CONSTRAINT FK_177FC39BFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participant CHANGE role role ENUM(\'participant\', \'referent\', \'peacekeeper\')');
        $this->addSql('ALTER TABLE event CHANGE type type ENUM(\'meeting\', \'protest\')');
        $this->addSql('ALTER TABLE member CHANGE role role ENUM(\'participant\', \'referent\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE news_group DROP FOREIGN KEY FK_177FC39BB5A459A0');
        $this->addSql('DROP TABLE material');
        $this->addSql('DROP TABLE news');
        $this->addSql('DROP TABLE news_group');
        $this->addSql('ALTER TABLE event CHANGE type type VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE member CHANGE role role VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE participant CHANGE role role VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
