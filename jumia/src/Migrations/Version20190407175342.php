<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190407175342 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE country (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(100) NOT NULL, code VARCHAR(3) NOT NULL, regex VARCHAR(25) NOT NULL)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__customer AS SELECT id, name, phone FROM customer');
        $this->addSql('DROP TABLE customer');
        $this->addSql('CREATE TABLE customer (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(50) DEFAULT NULL COLLATE BINARY, phone VARCHAR(50) DEFAULT NULL COLLATE BINARY)');
        $this->addSql('INSERT INTO customer (id, name, phone) SELECT id, name, phone FROM __temp__customer');
        $this->addSql('DROP TABLE __temp__customer');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE country');
        $this->addSql('CREATE TEMPORARY TABLE __temp__customer AS SELECT id, name, phone FROM customer');
        $this->addSql('DROP TABLE customer');
        $this->addSql('CREATE TABLE customer (id INTEGER DEFAULT NULL, name VARCHAR(50) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO customer (id, name, phone) SELECT id, name, phone FROM __temp__customer');
        $this->addSql('DROP TABLE __temp__customer');
    }
}
