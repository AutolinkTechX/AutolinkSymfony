<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250509143836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog_vote DROP FOREIGN KEY blog_vote_ibfk_1');
        $this->addSql('ALTER TABLE blog_vote DROP FOREIGN KEY blog_vote_ibfk_2');
        $this->addSql('DROP TABLE blog_vote');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CDAE07E97');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CDAE07E97 FOREIGN KEY (blog_id) REFERENCES blog (id)');
        $this->addSql('ALTER TABLE task_workshop DROP FOREIGN KEY task_workshop_ibfk_1');
        $this->addSql('ALTER TABLE task_workshop ADD date_debut DATETIME NOT NULL, ADD date_fin DATETIME NOT NULL, DROP starts_at, DROP ends_at, CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE description description VARCHAR(255) NOT NULL, CHANGE status status VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX workshop_id ON task_workshop');
        $this->addSql('CREATE INDEX IDX_DBAFE8881FDCE57C ON task_workshop (workshop_id)');
        $this->addSql('ALTER TABLE task_workshop ADD CONSTRAINT task_workshop_ibfk_1 FOREIGN KEY (workshop_id) REFERENCES workshop (id)');
        $this->addSql('ALTER TABLE workshop ADD nom VARCHAR(255) NOT NULL, ADD date_debut DATETIME NOT NULL, ADD date_fin DATETIME NOT NULL, ADD adresse VARCHAR(255) DEFAULT NULL, ADD latitude DOUBLE PRECISION DEFAULT NULL, ADD longitude DOUBLE PRECISION DEFAULT NULL, DROP name, DROP starts_at, DROP ends_at, DROP location, DROP price, DROP available_places, CHANGE description description VARCHAR(255) NOT NULL, CHANGE image image VARCHAR(255) NOT NULL, CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE workshop ADD CONSTRAINT FK_9B6F02C4A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_9B6F02C4A76ED395 ON workshop (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blog_vote (user_id INT NOT NULL, blog_id INT NOT NULL, vote_type VARCHAR(11) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX blogId (blog_id), INDEX IDX_BAC009F4A76ED395 (user_id), PRIMARY KEY(user_id, blog_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE blog_vote ADD CONSTRAINT blog_vote_ibfk_1 FOREIGN KEY (blog_id) REFERENCES blog (id)');
        $this->addSql('ALTER TABLE blog_vote ADD CONSTRAINT blog_vote_ibfk_2 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CDAE07E97');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CDAE07E97 FOREIGN KEY (blog_id) REFERENCES blog (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_workshop DROP FOREIGN KEY FK_DBAFE8881FDCE57C');
        $this->addSql('ALTER TABLE task_workshop ADD starts_at DATETIME DEFAULT NULL, ADD ends_at DATETIME DEFAULT NULL, DROP date_debut, DROP date_fin, CHANGE nom nom VARCHAR(100) NOT NULL, CHANGE description description TEXT DEFAULT NULL, CHANGE status status VARCHAR(50) DEFAULT NULL');
        $this->addSql('DROP INDEX idx_dbafe8881fdce57c ON task_workshop');
        $this->addSql('CREATE INDEX workshop_id ON task_workshop (workshop_id)');
        $this->addSql('ALTER TABLE task_workshop ADD CONSTRAINT FK_DBAFE8881FDCE57C FOREIGN KEY (workshop_id) REFERENCES workshop (id)');
        $this->addSql('ALTER TABLE workshop DROP FOREIGN KEY FK_9B6F02C4A76ED395');
        $this->addSql('DROP INDEX IDX_9B6F02C4A76ED395 ON workshop');
        $this->addSql('ALTER TABLE workshop ADD name VARCHAR(100) NOT NULL, ADD starts_at DATETIME DEFAULT NULL, ADD ends_at DATETIME DEFAULT NULL, ADD location VARCHAR(100) DEFAULT NULL, ADD price NUMERIC(10, 2) NOT NULL, ADD available_places INT NOT NULL, DROP nom, DROP date_debut, DROP date_fin, DROP adresse, DROP latitude, DROP longitude, CHANGE user_id user_id INT NOT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL, CHANGE description description TEXT DEFAULT NULL');
    }
}
