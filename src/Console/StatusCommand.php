<?php namespace Blade\Migrations\Laravel\Console;

use Blade\Migrations\Operation\StatusOperation;

/**
 * Показать список Миграций
 */
class StatusCommand extends \Illuminate\Console\Command
{
    protected $name = 'migrate:status';
    protected $description = 'Show the status of each migration';

    /**
     * @var StatusOperation
     */
    protected $operation;

    /**
     * Конструктор
     *
     * @param StatusOperation $operation
     */
    public function __construct(StatusOperation $operation)
    {
        parent::__construct();
        $this->operation = $operation;
    }


    /**
     * Run
     */
    public function fire()
    {
        $data = $this->operation->getData();
        if (!$data) {
            $this->error('No migrations found.');
            return;
        }

        $this->table(['', 'ID', 'Date', 'Name'], $data);
    }
}
