<?php namespace Usend\Migrations\Laravel\Log;

use Symfony\Component\Console\Output\OutputInterface;
use \Psr\Log\LogLevel;


class ConsoleOutputLogger extends \Psr\Log\AbstractLogger
{
    /**
     * @var OutputInterface
     */
    protected $output;


    /**
     * Конструктор
     *
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }


    /**
     * Лог
     *
     * @param string $level   - LogLevel::*
     * @param string $message
     * @param array  $context
     */
    public function log($level, $message, array $context = array())
    {
        $mappedLevel = 0;

        switch ($level) {
            // Показывать всегда независимо от настроек вербозности
            case LogLevel::EMERGENCY:
            case LogLevel::ALERT:
            case LogLevel::CRITICAL:
            case LogLevel::ERROR:
            case LogLevel::WARNING:
                $mappedLevel = 0;
                break;

            // Обычный вывод - ключ "-v"
            case LogLevel::NOTICE:
            case LogLevel::INFO:
                $mappedLevel = OutputInterface::VERBOSITY_VERBOSE;
                break;

            // Дебуг - ключ "-vvv"
            case LogLevel::DEBUG:
                $mappedLevel = OutputInterface::VERBOSITY_DEBUG;
                break;
        }

        if ($mappedLevel <= $this->output->getVerbosity()) {
            $this->output->writeln($message);
        }
    }

}
