<?php

namespace App\Controllers;


use App\DAO\AboutMeDAO;
use App\DTO\EmailDTO;
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

        //@TODO changes email send with email lib
        $emailDTO = new EmailDTO();
        $emailDTO->setName(htmlspecialchars($_POST['name']));
        $emailDTO->setMessage(htmlspecialchars($_POST['message']));
        $emailDTO->setSender(htmlspecialchars($_POST['email']));
        $emailDTO->setReceiver('contact@geoffroydutot.fr');
        $formcontent="From: ".$emailDTO->getName()." \n Message: ".$emailDTO->getMessage()."";
        $subject = "Contact Form";
        $mailheader = "From: ".$emailDTO->getSender()." \r\n";
        $headers = "MIME-Version: 1.0" . "\n";
        $headers .= "Content-type:text/html;charset=iso-8859-1" . "\n";
        $headers .= "From: ".$emailDTO->getSender();
        if (!mail($emailDTO->getReceiver(), $subject, $formcontent, $headers)) {
            $this->session['flash-error'] = "Erreur interne, votre message n'a pas pu être envoyé !";
            $this->redirect('/');
        }

        // Set success message
        $this->session['flash-success'] = "Votre message à bien été envoyé !";
        // Redirect home
        $this->redirect('/');
    }
}
