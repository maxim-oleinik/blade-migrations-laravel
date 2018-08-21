<?php namespace Blade\Migrations\Laravel\Console;

use Blade\Migrations\Operation\RollbackOperation;
use Illuminate\Console\ConfirmableTrait;
use Blade\Migrations\Laravel\Log\ConsoleOutputLogger;

/**
 * Откатить Миграцию
 */
class RollbackCommand extends \Illuminate\Console\Command
{
    use ConfirmableTrait;

    protected $signature = 'migrate:rollback
        {--force : Skip confirmation}
        {--id=  : Rollback selected migration by ID}
        {--load-file : Read SQL from file, not DB}';


    /**
     * @var RollbackOperation
     */
    protected $operation;

    /**
     * Конструктор
     *
     * @param RollbackOperation $operation
     */
    public function __construct(RollbackOperation $operation)
    {
        parent::__construct();
        $this->operation = $operation;
    }


    /**
     * Run
     */
    public function fire()
    {
        // Выставить МАХ уровень сообщений
        $this->getOutput()->setVerbosity(\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_DEBUG);

        $cmd = $this->operation;
        // Передать логгер в миграцию для дампа sql
        $cmd->setLogger(new ConsoleOutputLogger($this->getOutput()));
        $cmd->setForce($this->option('force'));

        $cmd->run(function ($migrationTitle) {
            return $this->confirmToProceed($migrationTitle, true);
        }, $this->option('id'), $this->option('load-file'));
    }
}
