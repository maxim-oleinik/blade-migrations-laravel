<?php namespace Usend\Migrations\Laravel;

use Illuminate\Support\ServiceProvider;
use Usend\Migrations\DbAdapterInterface;
use Usend\Migrations\MigrationService;
use Usend\Migrations\MigrationsRepository;


class MigrationsServiceProvider extends ServiceProvider
{
    protected $defer = true;


    /**
     * Register bindings
     */
    public function register()
    {
        $this->app->singleton(MigrationsRepository::class, function ($app) {
            return new MigrationsRepository(config('database.migrations.table'), $app[DbAdapterInterface::class]);
        });

        $this->app->singleton(MigrationService::class, function ($app) {
            return new MigrationService(config('database.migrations.dir'), $app[MigrationsRepository::class]);
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
            MigrationsRepository::class,
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
            ]);
        }
    }

}
