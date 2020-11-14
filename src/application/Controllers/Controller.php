<?php

namespace App\Controllers;

use App\Core\Twig;
use App\Helper\url_helper;

class Controller {

    use url_helper;

    private Twig $twig;
    protected $post;
    protected $session;
    protected array $flashMessage = [];

     public function __construct() {
         session_start();
         $this->twig = Twig::getInstance();
         $this->post = $_POST;
         $this->session = &$_SESSION;
         $this->setFlashMessage();
     }

     protected function render(string $path, array $data = [])
     {
         $data['error'] = $this->flashMessage['error'] ?? null;
         $data['success'] = $this->flashMessage['success'] ?? null;
         $data['warning'] = $this->flashMessage['warning'] ?? null;
         $data['info'] = $this->flashMessage['info'] ?? null;

         print_r($this->twig->render($path, $data));
    }

    protected function setFlashMessage()
    {
        if (!empty($this->session['flash-error'])) {
            $this->flashMessage['error'] = $this->session['flash-error'];
            unset($this->session['flash-error']);
        }
        if (!empty($this->session['flash-success'])) {
            $this->flashMessage['success'] = $this->session['flash-success'];
            unset($this->session['flash-success']);
        }
        if (!empty($this->session['flash-warning'])) {
            $this->flashMessage['warning'] = $this->session['flash-warning'];
            unset($this->session['flash-warning']);
        }
        if (!empty($this->session['flash-info'])) {
            $this->flashMessage['info'] = $this->session['flash-info'];
            unset($this->session['flash-info']);
        }
    }
}