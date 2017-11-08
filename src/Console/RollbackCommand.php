<?php namespace Usend\Migrations\Laravel\Console;

use Illuminate\Console\ConfirmableTrait;
use Usend\Migrations\Laravel\Log\ConsoleOutputLogger;
use Usend\Migrations\MigrationService;


class RollbackCommand extends \Illuminate\Console\Command
{
    use ConfirmableTrait;

    protected $signature = 'migrate:rollback';


    /**
     * The migrator instance.
     *
     * @var MigrationService
     */
    protected $migrator;

    /**
     * Конструктор
     *
     * @param  MigrationService $migrator
     */
    public function __construct(MigrationService $migrator)
    {
        parent::__construct();
        $this->migrator = $migrator;
    }


    /**
     * Run
     */
    public function fire()
    {
        // Выставить МАХ уровень сообщений
        $this->getOutput()->setVerbosity(\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_DEBUG);

        // Получить список миграций для запуска
        $migrations = $this->migrator->getRepository()->items(1);

        if (!$migrations) {
            $this->info('Nothing to rollback');
            return;
        }

        $next = current($migrations);

        if (!$this->confirmToProceed('Rollback: ' . $next->getName(), true)) {
            return;
        }

        // Передать логгер в миграцию для дампа sql
        $this->migrator->setLogger(new ConsoleOutputLogger($this->getOutput()));

        $this->migrator->down($next);

        $this->output->writeln('<info>Success</info>');
    }

}
