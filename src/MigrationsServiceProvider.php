<?php namespace Usend\Migrations\Laravel;

use Illuminate\Support\ServiceProvider;
use Usend\Migrations\DbAdapterInterface;
use Usend\Migrations\MigrationService;
use Usend\Migrations\Repository\DbRepository;
use Usend\Migrations\Repository\FileRepository;


class MigrationsServiceProvider extends ServiceProvider
{
    protected $defer = true;


    /**
     * Register bindings
     */
    public function register()
    {
        $this->app->singleton(DbRepository::class, function ($app) {
            return new DbRepository(config('database.migrations.table'), $app[DbAdapterInterface::class]);
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
                \Usend\Migrations\Laravel\Console\StatusCommand::class,
                \Usend\Migrations\Laravel\Console\InstallCommand::class,
                \Usend\Migrations\Laravel\Console\MigrateCommand::class,
                \Usend\Migrations\Laravel\Console\RollbackCommand::class,
                \Usend\Migrations\Laravel\Console\MakeCommand::class,
            ]);
        }
    }

}
