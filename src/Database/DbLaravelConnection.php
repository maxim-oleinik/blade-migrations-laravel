<?php namespace Blade\Migrations\Laravel\Database;

class DbLaravelConnection implements \Blade\Database\DbConnectionInterface
{
    /**
     * @var \Illuminate\Database\ConnectionInterface
     */
    private $connection;

    /**
     * Конструктор
     *
     * @param \Illuminate\Database\Connection $connection
     */
    public function __construct(\Illuminate\Database\Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return \Illuminate\Database\Connection
     */
    public function getConnection(): \Illuminate\Database\Connection
    {
        return $this->connection;
    }


    /**
     * @inheritdoc
     */
    public function execute($query, $bindings = []): int
    {
        return (int)$this->getConnection()->affectingStatement($query, $bindings);
    }

    /**
     * @inheritdoc
     */
    public function each($sql, $bindings = [], callable $callback)
    {
        $result = $this->getConnection()->select($sql, $bindings);
        if ($result) {
            foreach ($result as $row) {
                $callback((array)$row);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function beginTransaction()
    {
        $this->getConnection()->beginTransaction();
    }

    /**
     * @inheritdoc
     */
    public function commit()
    {
        $this->getConnection()->commit();
    }

    /**
     * @inheritdoc
     */
    public function rollBack()
    {
        $this->getConnection()->rollBack();
    }

    /**
     * @inheritdoc
     */
    public function escape($value): string
    {
        return substr($this->getConnection()->getPdo()->quote($value), 1, -1);
    }
}
