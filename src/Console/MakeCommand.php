<?php namespace Blade\Migrations\Laravel\Console;

use Illuminate\Console\Command;
use Blade\Migrations\Operation\MakeOperation;

/**
 * Создать файл миграции
 */
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
     * @var MakeOperation
     */
    protected $operation;

    /**
     * Констурктор
     *
     * @param MakeOperation $operation
     */
    public function __construct(MakeOperation $operation)
    {
        parent::__construct();
        $this->operation = $operation;
    }

    /**
     * Run
     */
    public function handle()
    {
        $this->info($this->operation->run($this->input->getArgument('name')));
    }
}
