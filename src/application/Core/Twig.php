<?php


namespace App\Core;


use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Twig
{
    private $loader;
    private $view;
    private static $_instance;

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new Twig();
        }

        return self::$_instance;
    }

    public function __construct()
    {
        $this->loader = new FilesystemLoader(__DIR__ . '/../templates');
        $this->view = new Environment($this->loader);
        $this->view->addGlobal('session', $_SESSION);
    }

    public function render(string $path, array $data = [])
    {
        return $this->view->render($path, $data);
    }
}