<?php namespace Usend\Migrations\Laravel\Console;

use Illuminate\Console\ConfirmableTrait;
use Usend\Migrations\Laravel\Log\ConsoleOutputLogger;
use Usend\Migrations\Migration;
use Usend\Migrations\MigrationService;


class MigrateCommand extends \Illuminate\Console\Command
{
    use ConfirmableTrait;

    protected $signature = 'migrate
        {--f|force : Не спрашивать подтверждение}
        {--auto : Запустить все миграции}
        {--up : Накатить только UP-миграции, без rollback}';

    /**
     * The migrator instance.
     *
     * @var MigrationService
     */
    protected $migrator;

    /**
     * Констурктор
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
        $migrations = $this->migrator->getDiff();

        if (!$migrations) {
            $this->info('Nothing to migrate');
            return;
        }

        // Передать логгер в миграцию для дампа sql
        $this->migrator->setLogger(new ConsoleOutputLogger($this->getOutput()));

        $c = 0;
        foreach ($migrations as $next) {
            if ($this->option('up') && !$next->isNew()) {
                continue;
            }
            $this->_process_migration($next);
            $c++;

            if (!$this->option('auto')) {
                break;
            }
        }

        if (!$c) {
            $this->info('Nothing to migrate');
            return;
        }

        $this->output->writeln('<info>Success</info>');
    }


    /**
     * Выполнить миграцию
     *
     * @param \Usend\Migrations\Migration $m
     */
    private function _process_migration(Migration $m)
    {
        // Спросить подтверждение
        $title = $m->getName();
        if ($m->isRemove()) {
            $title = 'Rollback: ' . $title;
        }
        if (!$this->option('force') && !$this->confirmToProceed($title, true)) {
            return;
        }

        if ($m->isNew()) {
            $this->migrator->up($m);
        } else {
            $this->migrator->down($m);
        }
    }

}
