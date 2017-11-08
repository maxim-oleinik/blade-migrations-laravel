<?php namespace Usend\Migrations\Laravel\Console;

use Illuminate\Console\ConfirmableTrait;
use Usend\Migrations\Laravel\Log\ConsoleOutputLogger;
use Usend\Migrations\MigrationService;


class MigrateCommand extends \Illuminate\Console\Command
{
    use ConfirmableTrait;

    protected $signature = 'migrate {--force}';

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

        $next = current($migrations);

        // Запускаем миграции только по одной
        if (!$this->confirmToProceed($next->getName(), true)) {
            return;
        }

        // Передать логгер в миграцию для дампа sql
        $this->migrator->setLogger(new ConsoleOutputLogger($this->getOutput()));

        if ($next->isNew()) {
            $this->migrator->up($next);
        } else {
            $this->migrator->down($next);
        }

        $this->output->writeln('<info>Success</info>');
    }

}
