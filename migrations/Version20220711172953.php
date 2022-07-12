<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220711172953 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Functions';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
        CREATE OR REPLACE FUNCTION to_real(IN any_value text, IN default_value real DEFAULT 0) RETURNS real AS $BODY$
        BEGIN
            RETURN any_value::real;
        EXCEPTION WHEN others THEN
            RETURN default_value;
        END;
        $BODY$ LANGUAGE plpgsql
        ');
    }
}
