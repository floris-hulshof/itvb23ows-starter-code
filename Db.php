<?php

class Db
{
    private $connection;

    public function __construct()
    {
        $host = "172.19.0.2";
        $username = "root";
        $password = "root";
        $database = 
        $this->connection = new mysqli($host, $username, $password, $database, $port);
        if ($this->connection->connect_error) {
            die('Connection failed: ' . $this->connection->connect_error);
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }
}

$database = new Database('172.19.0.2', 'root', 'root', 'hive', 3306);
