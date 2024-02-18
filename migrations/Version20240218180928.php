<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240218180928 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ordered uuid';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
        CREATE OR REPLACE FUNCTION o_uuid(IN created_at timestamptz DEFAULT NOW())
        RETURNS uuid LANGUAGE 'plpgsql' AS \$BODY\$
        BEGIN
            RETURN concat(
                overlay(substring(encode(int8send((
                    extract(epoch from created_at at time zone 'UTC')*1000000
                )::bigint), 'hex'), 2) placing '6' from 13 for 0),
                replace(substring(gen_random_uuid()::text, 20), '-', '')
            )::uuid;
        END \$BODY\$;
        ");

        $this->addSql("
        CREATE OR REPLACE FUNCTION o_uuid_to_timestamp(IN uid uuid)
        RETURNS timestamptz LANGUAGE 'plpgsql' AS \$BODY\$
        BEGIN
            RETURN to_timestamp((right(decode(concat('0', replace(
                overlay(substring(uid::text, 1, 18) placing '' from 15 for 1),
		    '-', '')), 'hex')::text, -1)::bit(64)::bigint)*0.000001);
        END \$BODY\$;
        ");
    }

    public function down(Schema $schema): void
    {
    }
}
