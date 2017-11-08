<?php namespace Usend\Migrations\Laravel\Console;

use Usend\Migrations\MigrationService;


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

            $data[] = [
                $status,
                $migration->getId(),
                $migration->isNew() ? '' : $migration->getDate()->format('d.m.Y H:i:s'),
                $name
            ];
        }

        $this->table(['', 'ID', 'Date', 'Name'], $data);
    }

}
