<?php

namespace App\Controllers;

use App\DAO\AboutMeDAO;
use App\DTO\EmailDTO;
use App\Form\FormValidator;

class HomeController extends Controller {

    public function index() {
        $data = [];
        $aboutMe = new AboutMeDAO();
        $aboutMe = $aboutMe->getAboutMe();
        $data['aboutMe'] = $aboutMe;

        $this->render('home.html.twig', $data);
    }

    public function contact() {
        if (empty($this->post)) {
            $this->redirect('/');
            //@TODO Display an error - empty data
        }

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

        if (!empty($form->validate($rules, $this->post))) {
            $this->redirect('/');
            //@TODO Display an error - invalid or missing data required
        }

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
        if (mail($emailDTO->getReceiver(), $subject, $formcontent, $headers)) {
            return true;
        } else {
            echo 'erreur';
        }
    }
}
