Database Migrations - Laravel
==========================
[![Latest Stable Version](https://poser.pugx.org/maxim-oleinik/blade-migrations-laravel/v/stable)](https://packagist.org/packages/maxim-oleinik/blade-migrations-laravel)
<a href="https://packagist.org/packages/maxim-oleinik/blade-migrations-laravel"><img src="https://poser.pugx.org/maxim-oleinik/blade-migrations-laravel/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/maxim-oleinik/blade-migrations-laravel"><img src="https://poser.pugx.org/maxim-oleinik/blade-migrations-laravel/license.svg" alt="License"></a>

Альтернатива стандартных **Миграций структуры БД** в **Laravel 5**

![](https://habrastorage.org/webt/9w/hm/1i/9whm1icwtg7per-15vfhyjejcx8.png)

Отличия
-----------
* **Миграции на чистом SQL**
    * вы используете все возможности своей БД для описания структуры и изменений
    * простая работа с процедурами и функциями
    * безопасные миграции данных (INSERT/UPDATE)
    * нативная подсветка синтаксиса в IDE
* **Запуск миграций в Транзакции**, если это поддерживает ваша БД (например PostgreSQL), с автоматическим откатом в случае ошибки
* **Динамический вывод в консоль SQL-команд**, которые вызываются на сервере
* **Автоматический откат миграций** при переключении с ветки на ветку (для проведения ревью, тестирования, демо, сборки на постоянной/staging базе данных)
* **Автообновление миграции при ее редактировании** (при смене версии в названии файла миграции)
* **Автоматическое тестирование rollback** в режиме `UD-DOWN-UP`
* **Откат или Reload любой выбранной миграции**


Требования
---------
* PHP >= 7.0
* Laravel >= 5.1 (поддерживает все версии 5.1 - 8.X)


Синтаксис
---------
* `--TRANSACTION` - если указано, то миграция будет запущена в транзакции
* Инструкции разделяются тегами `--UP` и `--DOWN`
* SQL запросы разделяются `";"` (последний символ в конце строки)
```
--TRANSACTION
--UP
ALTER TABLE authors ADD COLUMN code INT;
ALTER TABLE posts   ADD COLUMN slug TEXT;

--DOWN
ALTER TABLE authors DROP COLUMN code;
ALTER TABLE posts   DROP COLUMN slug;
```

**Если надо сменить разделитель**, когда в SQL необходимо использовать `";"`
```
--SEPARATOR=@
--UP
    ... some sql ...@
    ... some sql ...@

--DOWN
    ... some sql ...@
    ... some sql ...@
```




Установка и настройка
---------

1. Добавить с помощью **composer**
    ```
        composer require maxim-oleinik/blade-migrations-laravel
    ```

2. Настроить `config/database.php`
    ```
        'migrations' => [
            // Название таблицы в БД
            'table' => 'migrations',

            // Путь к директории с файлами миграций
            'dir'   => __DIR__ . '/../database/migrations',
        ],
    ```

3. Зарегистрировать ServiceProvider в `config/app.php`  
   для Laravel < 5.5
    ```
       'providers' => [
            ...
            Blade\Migrations\Laravel\MigrationsServiceProvider::class,
        ],
    ```

    для Laravel 6/7/8.X
    ```
    заменить
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class
    на
        Illuminate\Foundation\Providers\ComposerServiceProvider::class,
        Illuminate\Foundation\Providers\ArtisanServiceProvider::class
    чтобы отключить встроенные миграции
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

    # Накатить с ролбеком UP-DOWN-UP
    php artisan migrate -t

    # Автомиграция - удаляет все D-миграции, и накатывает все А-миграции
    php artisan migrate --auto

    # Накатить миграцию из указанного файла
    php artisan migrate FILE_NAME
```


### Rollback

При применении миграции (migrate) весь файл с SQL-командами сохраняется в БД. И потом откат делается из инструкций, сохраненных в базе.
Это сделано для того, чтобы иметь возможность откатить миграцию, когда проект переключается на версию, в которой нет этого файла миграции.
```
    # Откатить последнюю Y-миграцию
    php artisan migrate:rollback

    # Не спрашивать подтверждение
    php artisan migrate:rollback -f

    # Откатить миграцию по ее ID
    php artisan migrate:rollback --id=N

    # Откатить миграцию, инструкции загрузить из файла, а не из БД (например, если в базу попала ошибка)
    php artisan migrate:rollback --load-file
```


### Reload
Откатить миграцию и накатить ее заново
```
    php artisan migrate:reload

    # Ключи аналогичные rollback
    php artisan migrate:reload -f --id=N --load-file
```
