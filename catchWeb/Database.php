<?php

/**
 * Class Database
 * 資料庫相關方法
 */
class Database
{
    const DATABASE_HOST = 'localhost';
    const DATABASE_NAME = 'Betting';
    const DATABASE_USERNAME = 'root';
    const DATABASE_PASSWORD = '';

    private $connection = null;

    /**
     * 建立資料庫連線
     */
    public function __construct()
    {
        $dsn = sprintf('mysql:dbname=%s;host=%s', static::DATABASE_NAME, static::DATABASE_HOST);

        try {
            $this->connection = new PDO($dsn, static::DATABASE_USERNAME, static::DATABASE_PASSWORD);
            $this->connection->exec("set names utf8");
        } catch (PDOException $e) {
            echo 'Connection failed: '.$e->getMessage();
        }
    }

    /**
     * @param string SQL query
     * @return object
     */
    public function prepare($sql)
    {
        return $this->connection->prepare($sql);
    }

    public function transaction()
    {
        $this->connection->beginTransaction();
    }

    public function commit()
    {
        $this->connection->commit();
    }

    public function rollBack()
    {
        $this->connection->rollBack();
    }

    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
}