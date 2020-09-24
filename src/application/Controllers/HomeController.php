<?php

namespace App\Controllers;

use App\DAO\AboutMeDAO;
use App\DTO\EmailDTO;

class HomeController extends Controller {

    public function index() {
        $data = [];
        $aboutMe = new AboutMeDAO();
        $aboutMe = $aboutMe->getAboutMe();
        $data['aboutMe'] = $aboutMe;

        $this->render('home.html.twig', $data);
    }

    public function contact() {
        ini_set("smtp_port","25");;
        ini_set( 'display_errors', 1 );
        error_reporting( E_ALL );
        if (!empty($_POST['name']) && !empty($_POST['message']) && !empty($_POST['email'])) {
            if (filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) {
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
            } else {
                echo "Votre adresse email est invalide";
                return false;
            }
        } else {
            echo "Aucune données";
            return false;
        }
    }
}
