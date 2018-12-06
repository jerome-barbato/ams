<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181205185135 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE participant (user_id INT NOT NULL, event_id INT NOT NULL, role ENUM(\'participant\', \'referent\', \'peacekeeper\'), inscription DATE NOT NULL, INDEX IDX_D79F6B11A76ED395 (user_id), INDEX IDX_D79F6B1171F7E88B (event_id), PRIMARY KEY(user_id, event_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE auth_token (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, value VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_9315F04E1D775834 (value), INDEX IDX_9315F04EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE material (id INT AUTO_INCREMENT NOT NULL, place_id INT DEFAULT NULL, uuid VARCHAR(13) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, type VARCHAR(255) NOT NULL, size VARCHAR(255) DEFAULT NULL, quantity INT NOT NULL, image VARCHAR(255) DEFAULT NULL, theme VARCHAR(255) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, INDEX IDX_7CBE7595DA6A219 (place_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE material_user (material_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_D9FBBB7CE308AC6F (material_id), INDEX IDX_D9FBBB7CA76ED395 (user_id), PRIMARY KEY(material_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `group` (id INT AUTO_INCREMENT NOT NULL, uuid VARCHAR(13) NOT NULL, title VARCHAR(255) NOT NULL, image VARCHAR(200) DEFAULT NULL, description LONGTEXT DEFAULT NULL, creation DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE news (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, slug VARCHAR(200) NOT NULL, title VARCHAR(255) NOT NULL, text LONGTEXT DEFAULT NULL, created DATE NOT NULL, updated DATE NOT NULL, image VARCHAR(200) DEFAULT NULL, excerpt LONGTEXT DEFAULT NULL, INDEX IDX_1DD39950F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE news_group (news_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_177FC39BB5A459A0 (news_id), INDEX IDX_177FC39BFE54D947 (group_id), PRIMARY KEY(news_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, place_id INT DEFAULT NULL, uuid VARCHAR(13) NOT NULL, title VARCHAR(200) NOT NULL, description LONGTEXT DEFAULT NULL, creation DATE NOT NULL, begin DATE DEFAULT NULL, end DATE DEFAULT NULL, type ENUM(\'meeting\', \'protest\'), image VARCHAR(200) DEFAULT NULL, INDEX IDX_3BAE0AA7DA6A219 (place_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_group (event_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_2CDBF5E971F7E88B (event_id), INDEX IDX_2CDBF5E9FE54D947 (group_id), PRIMARY KEY(event_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE member (user_id INT NOT NULL, group_id INT NOT NULL, role ENUM(\'participant\', \'referent\'), inscription DATE NOT NULL, INDEX IDX_70E4FA78A76ED395 (user_id), INDEX IDX_70E4FA78FE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(200) DEFAULT NULL, address VARCHAR(200) NOT NULL, postal_code VARCHAR(6) NOT NULL, city VARCHAR(200) NOT NULL, country VARCHAR(200) NOT NULL, lat DOUBLE PRECISION NOT NULL, lng DOUBLE PRECISION NOT NULL, gid VARCHAR(200) DEFAULT NULL, UNIQUE INDEX UNIQ_741D53CD4C397118 (gid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, place_id INT DEFAULT NULL, uuid VARCHAR(13) NOT NULL, first_name VARCHAR(200) NOT NULL, last_name VARCHAR(200) NOT NULL, email VARCHAR(200) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, inscription DATE NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649DA6A219 (place_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B11A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B1171F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE auth_token ADD CONSTRAINT FK_9315F04EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE material ADD CONSTRAINT FK_7CBE7595DA6A219 FOREIGN KEY (place_id) REFERENCES place (id)');
        $this->addSql('ALTER TABLE material_user ADD CONSTRAINT FK_D9FBBB7CE308AC6F FOREIGN KEY (material_id) REFERENCES material (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE material_user ADD CONSTRAINT FK_D9FBBB7CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE news ADD CONSTRAINT FK_1DD39950F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE news_group ADD CONSTRAINT FK_177FC39BB5A459A0 FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE news_group ADD CONSTRAINT FK_177FC39BFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7DA6A219 FOREIGN KEY (place_id) REFERENCES place (id)');
        $this->addSql('ALTER TABLE event_group ADD CONSTRAINT FK_2CDBF5E971F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_group ADD CONSTRAINT FK_2CDBF5E9FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA78A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA78FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649DA6A219 FOREIGN KEY (place_id) REFERENCES place (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE material_user DROP FOREIGN KEY FK_D9FBBB7CE308AC6F');
        $this->addSql('ALTER TABLE news_group DROP FOREIGN KEY FK_177FC39BFE54D947');
        $this->addSql('ALTER TABLE event_group DROP FOREIGN KEY FK_2CDBF5E9FE54D947');
        $this->addSql('ALTER TABLE member DROP FOREIGN KEY FK_70E4FA78FE54D947');
        $this->addSql('ALTER TABLE news_group DROP FOREIGN KEY FK_177FC39BB5A459A0');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B1171F7E88B');
        $this->addSql('ALTER TABLE event_group DROP FOREIGN KEY FK_2CDBF5E971F7E88B');
        $this->addSql('ALTER TABLE material DROP FOREIGN KEY FK_7CBE7595DA6A219');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7DA6A219');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649DA6A219');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B11A76ED395');
        $this->addSql('ALTER TABLE auth_token DROP FOREIGN KEY FK_9315F04EA76ED395');
        $this->addSql('ALTER TABLE material_user DROP FOREIGN KEY FK_D9FBBB7CA76ED395');
        $this->addSql('ALTER TABLE news DROP FOREIGN KEY FK_1DD39950F675F31B');
        $this->addSql('ALTER TABLE member DROP FOREIGN KEY FK_70E4FA78A76ED395');
        $this->addSql('DROP TABLE participant');
        $this->addSql('DROP TABLE auth_token');
        $this->addSql('DROP TABLE material');
        $this->addSql('DROP TABLE material_user');
        $this->addSql('DROP TABLE `group`');
        $this->addSql('DROP TABLE news');
        $this->addSql('DROP TABLE news_group');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE event_group');
        $this->addSql('DROP TABLE member');
        $this->addSql('DROP TABLE place');
        $this->addSql('DROP TABLE user');
    }
}
