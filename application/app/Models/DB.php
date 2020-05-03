<?php

namespace RestAPI\Database;

use mysqli;

class DB
{
    protected $dbHost = 'restapi-db';
    protected $dbUser = 'root';
    protected $dbPass = '';
    protected $dbName = 'restapi';

    private $connection;
    protected static $_instance;

    public function __construct()
    {
        try {
            @$this->connection = new mysqli($this->dbHost, $this->dbUser, $this->dbPass, $this->dbName);

            if (isset($this->connection->connect_error)) {
                dd("Error: {$this->connection->connect_error}");
            }
        } catch (\Throwable $th) {
            dd("ERROR: {$th->getMessage()}");
        }
    }

    public static function getConnection(): mysqli
    {
        if (!self::$_instance) {
            self::$_instance = new DB;
        }

        return self::$_instance->connection;
    }
}
