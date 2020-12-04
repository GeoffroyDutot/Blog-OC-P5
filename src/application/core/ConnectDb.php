<?php

namespace App\core;

use App\Core\Config;
use PDO;
use Exception;

class ConnectDb
{
    private Config $config;
    private static ?ConnectDb $instance = null;
    private PDO $db;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new ConnectDb();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->config = Config::getInstance();
        try {
            $this->db = new PDO("mysql:host=".$this->config->getParam('hostname').";dbname=".$this->config->getParam('database'), $this->config->getParam('username'), $this->config->getParam('password'));
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            return $this->db;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getConnection() {
        return $this->db;
    }
}