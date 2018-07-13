<?php namespace Blade\Migrations\Laravel\Console;

use Illuminate\Console\Command;
use Blade\Migrations\Repository\DbRepository;


class InstallCommand extends Command
{
    protected $name = 'migrate:install';
    protected $description = 'Create the migration table';

    /**
     * The repository instance.
     *
     * @var DbRepository
     */
    protected $repository;

    /**
     * Конструктор
     *
     * @param  DbRepository $repository
     */
    public function __construct(DbRepository $repository)
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
