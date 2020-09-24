<?php

namespace App\Controllers;

use App\Core\Twig;
use App\Helper\url_helper;

class Controller {

    use url_helper;

    private Twig $twig;
    protected $post;
    protected $session;

     public function __construct() {
         session_start();
         $this->twig = Twig::getInstance();
         $this->post = $_POST;
         $this->session = &$_SESSION;
     }

     protected function render(string $path, array $data) {
         print_r($this->twig->render($path, $data));
    }
}