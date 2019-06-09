<?php namespace Blade\Migrations\Laravel\Console;

use Illuminate\Console\ConfirmableTrait;
use Blade\Migrations\Laravel\Log\ConsoleOutputLogger;
use Blade\Migrations\Operation\MigrateOperation;

/**
 * Накатить Миграции
 */
class MigrateCommand extends \Illuminate\Console\Command
{
    use ConfirmableTrait;

    protected $signature = 'migrate
        {--f|force : Skip confirmation}
        {--auto : Run ALL migrations with auto-remove}
        {--t|test : TEST Rollback}
        {name? : The path/name of the migration}';

    /**
     * @var MigrateOperation
     */
    protected $operation;

    /**
     * Констурктор
     *
     * @param MigrateOperation $operation
     */
    public function __construct(MigrateOperation $operation)
    {
        parent::__construct();
        $this->operation = $operation;
    }


    /**
     * Run
     */
    public function handle()
    {
        // Выставить МАХ уровень сообщений
        $this->getOutput()->setVerbosity(\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_DEBUG);

        $cmd = $this->operation;
        // Передать логгер в миграцию для дампа sql
        $cmd->setLogger(new ConsoleOutputLogger($this->getOutput()));

        $cmd->setAuto($this->option('auto'));
        $cmd->setForce($this->option('force'));
        $cmd->setTestRollback($this->option('test'));

        $cmd->run(function ($migrationTitle) {
            return $this->confirmToProceed($migrationTitle, true);
        }, $this->input->getArgument('name'));
    }
}
