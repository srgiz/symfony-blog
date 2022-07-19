<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220718175837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Category triggers';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
        CREATE OR REPLACE FUNCTION add_category_tree(IN NEW_CATEGORY_ID integer, IN NEW_PARENT_ID integer) RETURNS void AS \$\$
        DECLARE
            new_parent_level integer;
        BEGIN
            -- уровень вложенности нового родителя
            SELECT MAX(level) INTO new_parent_level FROM (
                (SELECT 0 AS level)
                UNION
                (SELECT level FROM category_tree WHERE category_id = NEW_PARENT_ID AND child_id = NEW_PARENT_ID LIMIT 1)
            ) t;
            
            INSERT INTO category_tree (category_id, child_id, level)
            -- добавляем все категории внутри которых есть NEW_PARENT_ID
            SELECT category_id, NEW_CATEGORY_ID, /*new_parent_level*/level + 1
            FROM category_tree
            WHERE child_id = NEW_PARENT_ID
            UNION
            -- добавляем ссылку на себя же
            SELECT NEW_CATEGORY_ID, NEW_CATEGORY_ID, new_parent_level + 1
            ;
        END;
        \$\$ LANGUAGE plpgsql
        ");

        $this->addSql("
        CREATE OR REPLACE FUNCTION move_category_tree(IN NEW_CATEGORY_ID integer, IN OLD_PARENT_ID integer, IN NEW_PARENT_ID integer) RETURNS void AS \$\$
        DECLARE
            old_parent_level integer;
            new_parent_level integer;
        BEGIN
            IF (COALESCE(OLD_PARENT_ID, 0) = COALESCE(NEW_PARENT_ID, 0)) THEN
                -- нечего перемещать
                RETURN;
            END IF;
            
            -- запрещаем рекурсивную привязку друг к другу
            IF (NEW_PARENT_ID IN (SELECT child_id FROM category_tree WHERE category_id = NEW_CATEGORY_ID)) THEN
                RAISE EXCEPTION 'Category % is a child of category %', NEW_PARENT_ID, NEW_CATEGORY_ID;
            END IF;
        
            -- уровень вложенности нового родителя
            SELECT MAX(level) INTO new_parent_level FROM (
                (SELECT 0 AS level)
                UNION
                (SELECT level FROM category_tree WHERE category_id = NEW_PARENT_ID AND child_id = NEW_PARENT_ID LIMIT 1)
            ) t;
                
            -- предыдущий уровень вложенности
            SELECT MAX(level) INTO old_parent_level FROM (
                (SELECT 0 AS level)
                UNION
                (SELECT level FROM category_tree WHERE category_id = OLD_PARENT_ID AND child_id = OLD_PARENT_ID LIMIT 1)
            ) t;
            
           DELETE FROM category_tree
           WHERE
                -- удаляем все дочерние связи к NEW_CATEGORY_ID
                child_id IN (
                    SELECT child_id FROM category_tree WHERE category_id = NEW_CATEGORY_ID
                )
                -- и только для того что выше по дереву
                AND category_id NOT IN (
                    SELECT child_id FROM category_tree WHERE category_id = NEW_CATEGORY_ID
                )
                -- т.е. внутреннюю вложенность не удаляем
           ;
                
           -- в новое верхнее дерево добавляем дочерние связи
           INSERT INTO category_tree (category_id, child_id, level)
           SELECT super_tree.category_id, sub_tree.child_id, sub_tree.level - old_parent_level + new_parent_level
           FROM category_tree AS super_tree
           CROSS JOIN category_tree AS sub_tree
           WHERE
                super_tree.child_id = NEW_PARENT_ID
                AND sub_tree.category_id = NEW_CATEGORY_ID
           ;
                
           -- обновляем уровень вложенности нетронутых связей
           UPDATE category_tree
           SET level = level - old_parent_level + new_parent_level
           WHERE category_id IN (
                SELECT child_id FROM category_tree WHERE category_id = NEW_CATEGORY_ID
           );
        END;
        \$\$ LANGUAGE plpgsql
        ");

        $this->addSql("
        CREATE OR REPLACE FUNCTION rebuild_category_tree() RETURNS void AS \$\$
        DECLARE
            r category%rowtype;
        BEGIN
            TRUNCATE category_tree RESTART IDENTITY;

            FOR r IN
                SELECT id, parent_id FROM category
                ORDER BY COALESCE(parent_id, 0), id
            LOOP
                EXECUTE add_category_tree(r.id, r.parent_id);
            END LOOP;
        END;
        \$\$ LANGUAGE plpgsql
        ");

        $this->addSql("
        CREATE OR REPLACE FUNCTION change_parent_category() RETURNS TRIGGER AS \$\$
        BEGIN
            IF (TG_OP = 'INSERT') THEN
                EXECUTE add_category_tree(NEW.id, NEW.parent_id);
            ELSIF (TG_OP = 'UPDATE') THEN
                EXECUTE move_category_tree(NEW.id, OLD.parent_id, NEW.parent_id);
            END IF;
            
            RETURN NULL;
        END;
        \$\$ LANGUAGE plpgsql
        ");

        $this->addSql('
        CREATE TRIGGER change_parent_category AFTER INSERT OR UPDATE ON category
            FOR EACH ROW EXECUTE FUNCTION change_parent_category()
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP FUNCTION change_parent_category() CASCADE');
        $this->addSql('DROP FUNCTION rebuild_category_tree');
        $this->addSql('DROP FUNCTION move_category_tree');
        $this->addSql('DROP FUNCTION add_category_tree');
    }
}
