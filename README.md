## Запуск

* `docker-compose up -d`
* `docker exec -i -t blog-php bash`
* `composer i`
* `php bin/console doctrine:migrations:migrate`

## Пользователи

Создать пользователя:

`php bin/console user:create email password`

Форма входа: `/login`
