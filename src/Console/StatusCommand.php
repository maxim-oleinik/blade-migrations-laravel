<?php namespace Blade\Migrations\Laravel\Console;

use Blade\Migrations\MigrationService;

class StatusCommand extends \Illuminate\Console\Command
{
    protected $name = 'migrate:status';
    protected $description = 'Show the status of each migration';

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
        $migrations = $this->migrator->status();

        if (!$migrations) {
            return $this->error('No migrations found.');
        }


        $data = [];
        $newMigrations = [];
        foreach ($migrations as $migration) {
            $name = $migration->getName();
            if ($migration->isNew()) {
                $status = '<comment>A</comment>';
                $name = "<comment>{$name}</comment>";
            } else if ($migration->isRemove()) {
                $status = '<fg=red>D</fg=red>';
                $name = "<fg=red>{$name}</fg=red>";
            } else {
                $status = '<info>Y</info>';
            }

            $row = [
                $status,
                $migration->getId(),
                $migration->isNew() ? '' : $migration->getDate()->format('d.m.Y H:i:s'),
                $name
            ];
            if ($migration->isNew()) {
                $newMigrations[] = $row;
            } else {
                $data[] = $row;
            }
        }

        $data = array_reverse($data);

        $this->table(['', 'ID', 'Date', 'Name'], array_merge($data, $newMigrations));
    }
}
