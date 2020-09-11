<?php

namespace App\Controllers;

use App\Core\Twig;

class Controller {

    private Twig $twig;

     public function __construct() {
         session_start();
         $this->twig = Twig::getInstance();
     }

     protected function render(string $path, array $data) {
         echo $this->twig->render($path, $data);
    }
}