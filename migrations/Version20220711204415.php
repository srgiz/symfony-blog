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
        CREATE OR REPLACE FUNCTION init_product_attribute_value() RETURNS TRIGGER AS $BODY$
        BEGIN
            -- Принудительное создание записи в таблице значений свойств
            INSERT INTO product_attribute_value (product_id)
            VALUES (NEW.id);
            
            RETURN NEW;
        END;
        $BODY$ LANGUAGE plpgsql
        ');

        $this->addSql('
        CREATE TRIGGER init_product_attribute_value AFTER INSERT ON product
            FOR EACH ROW EXECUTE FUNCTION init_product_attribute_value()
        ');

        $this->addSql("
        CREATE OR REPLACE FUNCTION throw_delete_product_attribute_value() RETURNS TRIGGER AS \$BODY\$
        DECLARE
            old_id bigint;
        BEGIN
            -- Запрет удаления записи значений свойств если товар все еще существует
            SELECT id INTO old_id FROM product WHERE id = OLD.product_id;
            
            IF (old_id IS NOT NULL) THEN
                RAISE EXCEPTION 'Product % already exists', old_id;
            END IF;
            
            RETURN OLD;
        END;
        \$BODY\$ LANGUAGE plpgsql
        ");

        $this->addSql('
        CREATE TRIGGER throw_delete_product_attribute_value BEFORE DELETE ON product_attribute_value
            FOR EACH ROW EXECUTE FUNCTION throw_delete_product_attribute_value()
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP FUNCTION throw_delete_product_attribute_value() CASCADE');
        $this->addSql('DROP FUNCTION init_product_attribute_value() CASCADE');
    }
}
