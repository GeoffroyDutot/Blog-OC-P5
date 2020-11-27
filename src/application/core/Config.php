<?php


namespace App\Core;


class Config
{
    private array $settings = [];
    private static $_instance;

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new Config();
        }

        return self::$_instance;
    }

    public function __construct()
    {
        $this->settings = include_once dirname(__DIR__) . '/../../config/config.php';
    }

    public function getParam(string $key): ?string
    {
        return $this->settings[$key] ?? null;
    }
}