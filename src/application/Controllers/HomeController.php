<?php

namespace App\Controllers;


use App\Core\Mail;
use App\DAO\AboutMeDAO;
use App\Form\FormValidator;

class HomeController extends Controller
{
    // Show homepage
    public function index()
    {
        // Get AboutMe infos
        $aboutMe = new AboutMeDAO();
        $aboutMe = $aboutMe->getAboutMe();

        // Set data from AboutMe infos
        $data = ['aboutMe' => $aboutMe];

        // Show homepage view
        $this->render('home.html.twig', $data);
    }

    // Send Email from contact form
    public function contact()
    {
        // Set error if empty data
        if (empty($this->post)) {
            // Set error empty data
            $this->session['flash-error'] = "Erreur, aucune donnée reçue !";
            // Redirect homepage
            $this->redirect('/');
        }

        // Set form validation rules
        $form = new FormValidator();
        $rules = [
            [
                'fieldName' => 'name',
                'type' => 'string',
                'minLength' => 3,
                'maxLength' => 255,
                'required' => true,
            ],
            [
                'fieldName' => 'email',
                'type' => 'email',
                'minLength' => 5,
                'maxLength' => 255,
                'required' => true,
            ],
            [
                'fieldName' => 'phone',
                'type' => 'phone',
                'minLength' => 5,
                'maxLength' => 255,
                'required' => false,
            ],
            [
                'fieldName' => 'message',
                'type' => 'string',
                'minLength' => 5,
                'maxLength' => 255,
                'required' => true,
            ]
        ];

        // Checks if is valid form data
        if (!empty($form->validate($rules, $this->post))) {
            // Set errors
            $this->session['form-errors'] = $form->getErrors();
            // Set inputs data
            $this->session['form-inputs'] = $this->post;
            // Redirect
            $this->redirect('/');
        }

        // Call Mail Class
        $mail = new Mail();

        // Checks if mail sended
        if (!$mail->sendMail($this->post['email'], $this->post['name'], $this->post['message'])) {
            // Set error - email error
            $this->session['flash-error'] = "Erreur interne, votre message n'a pas pu être envoyé !";
            // Set inputs data
            $this->session['form-inputs'] = $this->post;
            // Redirect
            $this->redirect('/');
        }

        // Set success message
        $this->session['flash-success'] = "Votre message à bien été envoyé !";
        // Redirect home
        $this->redirect('/');
    }
}