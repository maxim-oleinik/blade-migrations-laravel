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
        {--auto : Запустить все миграции}';

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
            // Пропускать только UP, если не укзан auto
            if (!$this->option('auto') && !$next->isNew()) {
                continue;
            }

            $title = $next->getName();
            if ($next->isRemove()) {
                $title = 'Rollback: ' . $title;
            }

            if ($this->option('force')) {
                if ($next->isNew()) {
                    $this->info($title);
                } else {
                    $this->error($title);
                }
            } else if (!$this->confirmToProceed($title, true)) {
                return;
            }

            if ($next->isNew()) {
                $this->migrator->up($next);
            } else {
                $this->migrator->down($next);
            }

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

}
