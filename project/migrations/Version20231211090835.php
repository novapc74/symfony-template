<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231211090835 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE page_block (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, html LONGTEXT DEFAULT NULL, layout VARCHAR(255) NOT NULL, sort SMALLINT DEFAULT NULL, INDEX IDX_E59A68F43DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE page_block ADD CONSTRAINT FK_E59A68F43DA5256D FOREIGN KEY (image_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE gallery ADD page_block_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE gallery ADD CONSTRAINT FK_472B783A6972852C FOREIGN KEY (page_block_id) REFERENCES page_block (id)');
        $this->addSql('CREATE INDEX IDX_472B783A6972852C ON gallery (page_block_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gallery DROP FOREIGN KEY FK_472B783A6972852C');
        $this->addSql('ALTER TABLE page_block DROP FOREIGN KEY FK_E59A68F43DA5256D');
        $this->addSql('DROP TABLE page_block');
        $this->addSql('DROP INDEX IDX_472B783A6972852C ON gallery');
        $this->addSql('ALTER TABLE gallery DROP page_block_id');
    }
}
