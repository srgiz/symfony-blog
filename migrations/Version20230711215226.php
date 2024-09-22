<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230711215226 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Blog';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TYPE enum_status AS ENUM ('draft', 'active')");

        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE post (id UUID NOT NULL, status enum_status NOT NULL, title VARCHAR(120) NOT NULL, content TEXT NOT NULL, preview TEXT DEFAULT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE post');

        $this->addSql('DROP TYPE enum_status');
    }
}
