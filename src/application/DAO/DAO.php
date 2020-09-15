<?php

namespace App\DAO;

use App\Core\Config;

class DAO
{
    private Config $config;
    private $db = null;

    public function __construct() {
        $this->config = Config::getInstance();
    }

    public function connectDb()
    {
        if (null !== $this->db) {
          return $this->db;
        }
        try {

            $this->db = new \PDO("mysql:host=".$this->config->getParam('hostname').";dbname=".$this->config->getParam('database'), $this->config->getParam('username'), $this->config->getParam('password'));
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $this->db;
        } catch (Exception $e) {
            exit();
        }
    }
}
