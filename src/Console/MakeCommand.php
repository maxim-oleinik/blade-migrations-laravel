<?php namespace Usend\Migrations\Laravel\Console;

use Illuminate\Console\Command;
use Usend\Migrations\Migration;
use Usend\Migrations\Repository\FileRepository;


class MakeCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'make:migration {name : The name of the migration}';
    protected $description = 'Create the migration file';

    /**
     * The repository instance.
     *
     * @var FileRepository
     */
    protected $repository;

    /**
     * Конструктор
     *
     * @param FileRepository $repository
     */
    public function __construct(FileRepository $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    /**
     * Run
     */
    public function fire()
    {
        $name = sprintf('%s_%s.sql',
            date('Ymd_His'),
            trim($this->input->getArgument('name'))
        );
        $migration = new Migration(null, $name);
        $migration->setSql(Migration::TAG_BEGIN.PHP_EOL.PHP_EOL.Migration::TAG_DOWN.PHP_EOL);
        $this->repository->insert($migration);
        $this->info($name);
    }

}
