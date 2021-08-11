<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210811113815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE log_entity (uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(16) NOT NULL COLLATE `utf8mb4_bin`, change_set JSON NOT NULL, created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6) NOT NULL COMMENT \'(DC2Type:timestamp_immutable_ms)\', PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log_entity_relation (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', related VARCHAR(255) NOT NULL COLLATE `utf8mb4_bin`, INDEX IDX_429353F9D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE log_entity_relation ADD CONSTRAINT FK_429353F9D17F50A6 FOREIGN KEY (uuid) REFERENCES log_entity (uuid) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE log_entity_relation DROP FOREIGN KEY FK_429353F9D17F50A6');
        $this->addSql('DROP TABLE log_entity');
        $this->addSql('DROP TABLE log_entity_relation');
    }
}
