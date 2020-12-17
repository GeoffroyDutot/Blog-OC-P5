<?php

namespace App\DAO;

use PDO;
use App\Core\ConnectDb;

Abstract class DAO
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = (ConnectDb::getInstance())->getConnection();
    }
}