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
        CREATE OR REPLACE FUNCTION change_parent_category() RETURNS TRIGGER AS \$\$
        DECLARE
            old_parent_level integer;
            new_parent_level integer;
        BEGIN
            -- уровень вложенности нового родителя
            SELECT MAX(level) INTO new_parent_level FROM (
                (SELECT 0 AS level)
                UNION
                (SELECT level FROM category_tree WHERE category_id = NEW.parent_id AND child_id = NEW.parent_id LIMIT 1)
            ) t;
            
            IF (TG_OP = 'INSERT') THEN
                INSERT INTO category_tree (category_id, child_id, level)
                -- добавляем все категории внутри которых есть NEW.parent_id
                SELECT category_id, NEW.id, level + 1
                FROM category_tree
                WHERE child_id = NEW.parent_id
                UNION
                -- добавляем ссылку на себя же
                SELECT NEW.id, NEW.id, new_parent_level + 1
                ;
            ELSIF (TG_OP = 'UPDATE' AND COALESCE(OLD.parent_id, 0) != COALESCE(NEW.parent_id, 0)) THEN
                -- запрещаем рекурсивную привязку друг к другу
                IF (NEW.parent_id IN (SELECT child_id FROM category_tree WHERE category_id = NEW.id)) THEN
                    RAISE EXCEPTION 'Category % is a child of category %', NEW.parent_id, NEW.id;
                END IF;
                
                -- предыдущий уровень вложенности
                SELECT MAX(level) INTO old_parent_level FROM (
                    (SELECT 0 AS level)
                    UNION
                    (SELECT level FROM category_tree WHERE category_id = OLD.parent_id AND child_id = OLD.parent_id LIMIT 1)
                ) t;
            
                DELETE FROM category_tree
                WHERE
                    -- удаляем все дочерние связи к NEW.id
                    child_id IN (
                        SELECT child_id FROM category_tree WHERE category_id = NEW.id
                    )
                    -- и только для того что выше по дереву
                    AND category_id NOT IN (
                        SELECT child_id FROM category_tree WHERE category_id = NEW.id
                    )
                    -- т.е. внутреннюю вложенность не удаляем
                ;
                
                -- в новое верхнее дерево добавляем дочерние связи
                INSERT INTO category_tree (category_id, child_id, level)
                SELECT super_tree.category_id, sub_tree.child_id, sub_tree.level - old_parent_level + new_parent_level
                FROM category_tree AS super_tree
                CROSS JOIN category_tree AS sub_tree
                WHERE
                    super_tree.child_id = NEW.parent_id
                    AND sub_tree.category_id = NEW.id
                ;
                
                -- обновляем уровень вложенности нетронутых связей
                UPDATE category_tree
                SET level = level - old_parent_level + new_parent_level
                WHERE category_id IN (
                    SELECT child_id FROM category_tree WHERE category_id = NEW.id
                );
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
    }
}
