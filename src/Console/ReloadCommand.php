<?php namespace Blade\Migrations\Laravel\Console;

use Blade\Migrations\Operation\MigrateOperation;
use Blade\Migrations\Operation\RollbackOperation;
use Illuminate\Console\ConfirmableTrait;
use Blade\Migrations\Laravel\Log\ConsoleOutputLogger;

/**
 * Откатить Миграцию
 */
class ReloadCommand extends \Illuminate\Console\Command
{
    use ConfirmableTrait;

    protected $description = 'Rollback migration and UP it again';
    protected $signature = 'migrate:reload
        {--f|force : Skip confirmation}
        {--id=  : Reload selected migration by ID}
        {--l|load-file : Read SQL from file, not DB}';


    /**
     * @var RollbackOperation
     */
    protected $opRollback;

    /**
     * @var MigrateOperation
     */
    protected $opMigrate;

    /**
     * Конструктор
     *
     * @param RollbackOperation $opRollback
     * @param MigrateOperation  $opMigrate
     */
    public function __construct(RollbackOperation $opRollback, MigrateOperation $opMigrate)
    {
        parent::__construct();
        $this->opRollback = $opRollback;
        $this->opMigrate  = $opMigrate;
    }


    /**
     * Run
     */
    public function handle()
    {
        // Выставить МАХ уровень сообщений
        $this->getOutput()->setVerbosity(\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_DEBUG);

        $this->opRollback->setLogger($logger = new ConsoleOutputLogger($this->getOutput()));
        $this->opRollback->setForce($this->option('force'));
        $migration = $this->opRollback->run(function ($migrationTitle) {
            return $this->confirmToProceed($migrationTitle, true);
        }, $this->option('id'), $this->option('load-file'), true);

        if ($migration) {
            $this->opMigrate->setLogger($logger);
            $this->opMigrate->setForce(true);
            $this->opMigrate->run(function () {
                return true;
            });
        }
    }
}
