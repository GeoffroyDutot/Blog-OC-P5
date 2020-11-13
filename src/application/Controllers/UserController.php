<?php


namespace App\Controllers;


use App\DAO\AboutMeDAO;
use App\DAO\UserDAO;
use App\DTO\UserDTO;
use App\Form\FormValidator;


class UserController extends Controller {


    public function register() {
        $data = [];

        $aboutMe = new AboutMeDAO();
        $aboutMe = $aboutMe->getAboutMe();
        $data['aboutMe'] = $aboutMe;

        $this->render('register.html.twig', $data);
    }

    public function addUser() {
        if (empty($this->post)) {
            echo 'Aucunes données.';
            return;
        }

        $form = new FormValidator();
        $rules = [
            [
                'fieldName' => 'email',
                'type' => 'email',
                'minLength' => 5,
                'maxLength' => 255,
                'required' => true,
            ],
            [
                'fieldName' => 'password',
                'type' => 'string',
                'minLength' => 5,
                'maxLength' => 255,
                'required' => true,
            ],
            [
                'fieldName' => 'pseudo',
                'type' => 'string',
                'minLength' => 3,
                'maxLength' => 255,
                'required' => true,
            ]
        ];

        if (!empty($form->validate($rules, $_POST))) {
            echo 'Formulaire non valide, vérifiez les informations';
            return;
        }

        $userDTO = new UserDTO();
        $userDTO->setEmail($this->post['email']);
        $userDTO->setPseudo($this->post['pseudo']);
        $userDTO->setPassword(password_hash($this->post['password'], PASSWORD_BCRYPT));
        $userDTO->setRole('ROLE_USER');
        $userDTO->setDateRegistered(date('Y-m-d H:i:s'));
        $userDTO->setIsDeactivated(false);
        $userDTO->setReasonDeactivation(null);
        $userDTO->setDeactivatedAt(null);
        $user = new UserDAO();

        if (!empty($user->getUserByEmail($userDTO->getEmail()))) {
            echo 'User with this email already exists';
            return;
        }

        if (!empty($user->getUserByPseudo($userDTO->getPseudo()))) {
            echo 'User with this pseudo already exists';
            return;
        }

        $user = $user->save($userDTO);
        if ($user) {
            $this->session['email'] = $userDTO->getEmail();
            $this->session['pseudo'] = $userDTO->getPseudo();
            $this->redirect('/');
        }  else {
            echo 'Erreur, l\'utilisateut n\'as pas pu être ajouté';
            return;
        }
    }

    public function login() {
        $data = [];

        if (isset($this->session['email']) && isset($this->session['role']) && isset($this->session['pseudo'])) {
            $this->redirect('/');
        }

        $aboutMe = new AboutMeDAO();
        $aboutMe = $aboutMe->getAboutMe();
        $data['aboutMe'] = $aboutMe;

        $this->render('login.html.twig', $data);
    }

    public function authenticate() {
        if (empty($this->post)) {
            echo 'Aucunes données.';
            return;
        }

        $form = new FormValidator();
        $rules = [
            [
                'fieldName' => 'email',
                'type' => 'email',
                'minLength' => 5,
                'maxLength' => 255,
                'required' => true,
            ],
            [
                'fieldName' => 'password',
                'type' => 'string',
                'minLength' => 5,
                'maxLength' => 255,
                'required' => true,
            ]
        ];

        if (!empty($form->validate($rules, $_POST))) {
            echo 'Formulaire non valide, vérifiez les informations';
            return;
        }

        $user = new UserDAO();
        $user = $user->getUserByEmail($this->post['email']);
        if (empty($user)) {
            echo 'Aucun utilisateur correspondant à cette adresse email à été trouvé';
            return;
        }

        if (!password_verify($this->post['password'], $user->getPassword())) {
             echo 'Error incorrect password';
             return;
        }

        $this->session['email'] = $user->getEmail();
        $this->session['pseudo'] = $user->getPseudo();
        $this->session['role'] = $user->getRole();
        $this->session['profilPicture'] = $user->getProfilPicture();

        ($this->session['role'] === 'ROLE_ADMIN') ? $this->redirect('/admin/tableau-de-bord') : $this->redirect('/');
    }

    public function logout() {
        session_destroy();
        echo 'vous êtes déconnecté';
        $this->redirect('/');
    }
}