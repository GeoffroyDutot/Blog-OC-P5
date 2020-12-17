<?php

namespace App\Controllers;


use App\Core\Twig;
use App\Helper\SlugifyHelper;
use App\Helper\UrlHelper;

class Controller
{
    use UrlHelper;
    use SlugifyHelper;

    private Twig $twig;
    protected $post;
    protected $session;
    protected array $flashMessage = [];
    protected array $formErrors = [];
    protected array $formInputs = [];

     public function __construct()
     {
         session_start();
         $this->twig = Twig::getInstance();
         $this->post = $_POST;
         $this->session = &$_SESSION;
         $this->setFlashMessage();
         $this->setFormErrors();
         $this->setFormInputs();
     }

     // Render view with data
     protected function render(string $path, array $data = [])
     {
         // Set data with flash-messages
         $data['error'] = $this->flashMessage['error'] ?? null;
         $data['success'] = $this->flashMessage['success'] ?? null;
         $data['warning'] = $this->flashMessage['warning'] ?? null;
         $data['info'] = $this->flashMessage['info'] ?? null;
         $data['formErrors'] = $this->formErrors['form-errors'] ?? null;
         $data['formInputs'] = $this->formInputs['form-inputs'] ?? null;

         // Render view
         print_r($this->twig->render($path, $data));
    }

    // Set messages from session
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

    // Set form errors data from session
    protected function setFormErrors()
    {
        if (!empty($this->session['form-errors'])) {
            $this->formErrors['form-errors'] = $this->session['form-errors'];
            unset($this->session['form-errors']);
        }
    }

    // Set form data inputs from session
    protected function setFormInputs()
    {
        if (!empty($this->session['form-inputs'])) {
            $this->formInputs['form-inputs'] = $this->session['form-inputs'];
            unset($this->session['form-inputs']);
        }
    }
}