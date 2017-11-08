<?php namespace Usend\Migrations\Laravel\Console;

use Illuminate\Console\Command;
use Usend\Migrations\MigrationsRepository;


class InstallCommand extends Command
{
    protected $name = 'migrate:install';
    protected $description = 'Create the migration table';

    /**
     * The repository instance.
     *
     * @var MigrationsRepository
     */
    protected $repository;

    /**
     * Конструктор
     *
     * @param  MigrationsRepository  $repository
     */
    public function __construct(MigrationsRepository $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    /**
     * Run
     */
    public function fire()
    {
        $this->repository->install();
        $this->info('Migration table created successfully');
    }

}
