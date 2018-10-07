Blade/Migrations - Laravel
==========================

Набор консольных комманд под Laravel/Artisan  
Используют текущее соединение с базой в вашем laravel-проекте.  
См. https://github.com/maxim-oleinik/blade-migrations

Установка и настройка
---------

1. Добавить в **composer**
    ```
        composer require maxim-oleinik/blade-migrations-laravel
    ```

2. Настроить `config/database.php`
    ```
        'migrations' => [
            'table' => 'migrations', // Название таблицы в БД
            'dir'   => __DIR__ . '/../database/migrations', // Путь к директории с файлами миграций
        ],
    ```

3. Зарегистрировать ServiceProvider в `config/app.php`
    ```
       'providers' => [
            ...
            Blade\Migrations\Laravel\MigrationsServiceProvider::class,
        ],
    ```

4. Создать таблицу миграций в БД
    ```
        php artisan migrate:install
    ```


Команды
---------

### Создать миграцию
```
    php artisan make:migration NAME
```
см. синтаксис https://github.com/maxim-oleinik/blade-migrations


### Status
```
    php artisan migrate:status

    +---+----+---------------------+------------------------+
    |   | ID | Date                | Name                   |
    +---+----+---------------------+------------------------+
    | Y | 6  | 28.08.2018 20:17:01 | 20180828_195348_M1.sql |
    | D | 7  | 28.08.2018 20:17:21 | 20180828_201639_M3.sql |
    | A |    |                     | 20180828_200950_M2.sql |
    +---+----+---------------------+------------------------+
```
где:
* **Y** - выполнена
* **D** - требует отката (в текущей ветке ее нет)
* **A** - в очереди


### Migrate
```
    # Накатить следующую по очереди А-миграцию
    php artisan migrate

    # Не спрашивать подтверждение
    php artisan migrate -f

    # Автомиграция - удаляет D-миграции, накатывает А-миграции
    php artisan migrate --auto

    # Накатить миграцию из указанного файла
    php artisan migrate FILE_NAME
```


### Rollback
```
    # Откатить последнюю Y-миграцию
    php artisan migrate:rollback

    # Не спрашивать подтверждение
    php artisan migrate:rollback -f

    # Откатить миграцию по ее номеру
    php artisan migrate:rollback --id=N

    # Откатить миграцию, инструкции загрузить из файла, а не из БД (например, если в базу попала ошибка)
    php artisan migrate:rollback --load-file
```
