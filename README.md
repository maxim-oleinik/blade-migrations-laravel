Database Migrations - Laravel
==========================

[rus](./README.rus.md) /
[![Latest Stable Version](https://poser.pugx.org/maxim-oleinik/blade-migrations-laravel/v/stable)](https://packagist.org/packages/maxim-oleinik/blade-migrations-laravel)
<a href="https://packagist.org/packages/maxim-oleinik/blade-migrations-laravel"><img src="https://poser.pugx.org/maxim-oleinik/blade-migrations-laravel/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/maxim-oleinik/blade-migrations-laravel"><img src="https://poser.pugx.org/maxim-oleinik/blade-migrations-laravel/license.svg" alt="License"></a>

An intelligent alternative version of **Laravel 5 Database Migrations**

![](https://habrastorage.org/webt/9w/hm/1i/9whm1icwtg7per-15vfhyjejcx8.png)

Features
-----------
* **Using raw SQL queries**
    * you can use all the capabilities of your database to describe the structure and changes
    * easy work with procedures and functions
    * safe data migrations (INSERT/UPDATE)
    * IDE native syntax support
* **Running migrations within a transaction** with automatic rollback in case of an error (if your database supports it, PostgreSQL for example)
* **Dynamic output running the SQL-queries**
* **Automatic rollback** after switching the branch (for reviewing, testing, demo, building at permanent/staging database)
* **Auto-update the migration after editing** (version change in the name of the migration file)
* **Apply with rollback testing** - `UD-DOWN-UP`
* **Rollback or Reload any selected migration**


Requirements
---------
* PHP >= 7.0
* Laravel >= 5.1/6.X (supports all versions 5.1 - 6.X)


Syntax
---------
* `--TRANSACTION` - if specified, the migration will be launched within a transaction
* Instructions are separated by `--UP` and` --DOWN` tags.
* The SQL queries are separated by `";"` (the last character at the end of the line)

```
--TRANSACTION
--UP
ALTER TABLE authors ADD COLUMN code INT;
ALTER TABLE posts   ADD COLUMN slug TEXT;

--DOWN
ALTER TABLE authors DROP COLUMN code;
ALTER TABLE posts   DROP COLUMN slug;
```

**If you need to change the delimiter** (when in SQL you have to use `";"`)
```
--SEPARATOR=@
--UP
    ... some sql ...@
    ... some sql ...@

--DOWN
    ... some sql ...@
    ... some sql ...@
```


Install
---------

1. Require this package with **composer** using the following command:
    ```
        composer require maxim-oleinik/blade-migrations-laravel
    ```

2. Update `config/database.php`
    ```
        'migrations' => [
            // migrations table name
            'table' => 'migrations',

            // path to migrations dir
            'dir'   => __DIR__ . '/../database/migrations',
        ],
    ```

3. Register ServiceProvider at `config/app.php`
   for Laravel < 5.5
    ```
       'providers' => [
            ...
            Blade\Migrations\Laravel\MigrationsServiceProvider::class,
        ],
    ```

    for Laravel 6.X replace
    ```
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class
        # with
        Illuminate\Foundation\Providers\ComposerServiceProvider::class,
        Illuminate\Foundation\Providers\ArtisanServiceProvider::class
    ```

4. Create migration table
    ```
        php artisan migrate:install
    ```


Usage
---------

### Create Migration file
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
* **Y** - applied migration
* **D** - have to rollback (no this migration in the current branch/revision)
* **A** - not applied yet, next to be run


### Migrate
```
    # Apply next А-migration
    php artisan migrate

    # Apply the migration without a prompt
    php artisan migrate -f

    # Apply with rollback testing: UP-DOWN-UP
    php artisan migrate -t

    # Auto-migrate all - rollback all D-migrations and appply all А-migrations
    php artisan migrate --auto

    # Apply migration from the specified file
    php artisan migrate FILE_NAME
```


### Rollback

The migrate file with SQL-commands is saved to DB after applying the migration. So the rollback is processing from this saved instructions.
This is done to be able to rollback the migration when project switches to another branch which does not contains this file.
```
    # Rollback the latest Y-migration
    php artisan migrate:rollback

    # To force the rollback without a prompt
    php artisan migrate:rollback -f

    # Rollback migration by its ID
    php artisan migrate:rollback --id=N

    # Rollback migration with commands taken from migration file, not from DB (if saved version contains error)
    php artisan migrate:rollback --load-file
```


### Reload

Rollback migration and run it again
```
    php artisan migrate:reload

    # the same options as rollback
    php artisan migrate:reload -f --id=N --load-file
```
