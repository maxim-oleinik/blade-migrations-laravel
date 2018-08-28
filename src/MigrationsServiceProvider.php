<?php namespace Blade\Migrations\Laravel;

use Blade\Database\Sql\SqlBuilder;
use Illuminate\Support\ServiceProvider;
use Blade\Database\DbAdapter;
use Blade\Migrations\Laravel\Database\DbLaravelConnection;
use Blade\Migrations\MigrationService;
use Blade\Migrations\Repository\DbRepository;
use Blade\Migrations\Repository\FileRepository;


class MigrationsServiceProvider extends ServiceProvider
{
    protected $defer = true;


    /**
     * Register bindings
     */
    public function register()
    {
        $this->app->singleton(DbAdapter::class, function ($app) {
            $conn = new DbLaravelConnection($app[\Illuminate\Database\ConnectionInterface::class]);
            SqlBuilder::setEscapeMethod([$conn, 'escape']);
            return new DbAdapter($conn);
        });
        $this->app->singleton(DbRepository::class, function ($app) {
            return new DbRepository(config('database.migrations.table'), $app[DbAdapter::class]);
        });
        $this->app->singleton(FileRepository::class, function ($app) {
            return new FileRepository(config('database.migrations.dir'));
        });
        $this->app->singleton(MigrationService::class, function ($app) {
            return new MigrationService($app[FileRepository::class], $app[DbRepository::class]);
        });
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            DbAdapter::class,
            DbRepository::class,
            FileRepository::class,
            MigrationService::class,
        ];
    }


    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Blade\Migrations\Laravel\Console\StatusCommand::class,
                \Blade\Migrations\Laravel\Console\InstallCommand::class,
                \Blade\Migrations\Laravel\Console\MigrateCommand::class,
                \Blade\Migrations\Laravel\Console\RollbackCommand::class,
                \Blade\Migrations\Laravel\Console\MakeCommand::class,
            ]);
        }
    }
}
