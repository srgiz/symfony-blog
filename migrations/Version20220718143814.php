<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220718143814 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id SERIAL NOT NULL, parent_id INT DEFAULT NULL, uid VARCHAR(128) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX parent_id ON category (parent_id)');
        $this->addSql('CREATE UNIQUE INDEX uid ON category (uid)');
        $this->addSql('CREATE TABLE category_tree (id BIGSERIAL NOT NULL, category_id INT NOT NULL, child_id INT NOT NULL, level INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX category_id ON category_tree (category_id)');
        $this->addSql('CREATE INDEX child_id ON category_tree (child_id)');
        $this->addSql('CREATE UNIQUE INDEX unique_row ON category_tree (category_id, child_id)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1727ACA70 FOREIGN KEY (parent_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category_tree ADD CONSTRAINT FK_3CA5249B12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category_tree ADD CONSTRAINT FK_3CA5249BDD62C21B FOREIGN KEY (child_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP CONSTRAINT FK_64C19C1727ACA70');
        $this->addSql('ALTER TABLE category_tree DROP CONSTRAINT FK_3CA5249B12469DE2');
        $this->addSql('ALTER TABLE category_tree DROP CONSTRAINT FK_3CA5249BDD62C21B');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_tree');
    }
}
