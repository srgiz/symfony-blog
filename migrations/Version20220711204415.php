<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220711204415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Product triggers';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
        CREATE OR REPLACE FUNCTION insert_product_values() RETURNS TRIGGER AS
        $BODY$
        BEGIN
            INSERT INTO product_attribute_value (product_id)
            VALUES (NEW.id);
            
            RETURN NEW;
        END;
        $BODY$
        LANGUAGE plpgsql;
        ');

        $this->addSql('
        CREATE TRIGGER insert_product_values AFTER INSERT ON product
            FOR EACH ROW EXECUTE FUNCTION insert_product_values()
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TRIGGER insert_product_values ON product');
    }
}
