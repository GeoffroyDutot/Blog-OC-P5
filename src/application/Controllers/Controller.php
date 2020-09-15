<?php

namespace App\Controllers;

use App\Core\Twig;

class Controller {

    private Twig $twig;
    protected $post;

     public function __construct() {
         session_start();
         $this->twig = Twig::getInstance();
         $this->post = $_POST;
     }

     protected function render(string $path, array $data) {
         print_r($this->twig->render($path, $data));
    }
}