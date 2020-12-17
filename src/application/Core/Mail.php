<?php


namespace App\Core;


use App\Core\Config;
use PHPMailer\PHPMailer\PHPMailer;

class Mail {

    private Config $config;

    public function __construct()
    {
        $this->config = Config::getInstance();
    }

    public function sendMail(string $email, string $name, string $content)
    {
        $mail = new PHPMailer;

        $mail->isSMTP();
        $mail->Host       = $this->config->getParam('emailHost');
        $mail->SMTPAuth   = true;
        $mail->Username   = $this->config->getParam('email');
        $mail->Password   = $this->config->getParam('emailPassword');
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;


        $mail->setFrom($this->config->getParam('email'), $name);
        $mail->addAddress($this->config->getParam('email'), 'Blog');
        $mail->addReplyTo($email);

        $mail->isHTML(true);
        $mail->Subject = 'Contact Form : Blog';
        $mail->Body    = $content;

        if (!$mail->send()) {
            return false;
        }

        return true;
    }
}