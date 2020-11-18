<?php


namespace App\Controllers;


use App\DAO\AboutMeDAO;
use App\DAO\UserDAO;
use App\DTO\UserDTO;
use App\Form\FormValidator;


class UserController extends Controller {


    public function register() {
        $aboutMe = new AboutMeDAO();
        $aboutMe = $aboutMe->getAboutMe();
        $data = ['aboutMe' => $aboutMe];

        $this->render('register.html.twig', $data);
    }

    public function addUser() {
        if (empty($this->post)) {
            $this->session['flash-error'] = "Aucune donnée reçue !";
            $this->redirect('/inscription');
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
            $this->session['flash-error'] = "Un compte avec cette adresse email existe déjà !";
            $this->redirect('/inscription');
        }

        if (!empty($user->getUserByPseudo($userDTO->getPseudo()))) {
            $this->session['flash-error'] = "Un compte avec ce pseudo existe déjà !";
            $this->redirect('/inscription');
        }

        $user = $user->save($userDTO);
        if ($user) {
            $this->session['email'] = $userDTO->getEmail();
            $this->session['pseudo'] = $userDTO->getPseudo();
            $this->session['flash-success'] = "Vous êtes désormais inscrit. Bienvenue !";
            $this->redirect('/');
        }  else {
            $this->session['flash-error'] = "Erreur interne, votre compte n'a pu être créée";
            $this->redirect('/inscription');
        }
    }

    public function login() {
        if (isset($this->session['email']) && isset($this->session['role']) && isset($this->session['pseudo'])) {
            $this->session['flash-error'] = "Vous êtes déjà connecté !";
            $this->redirect($_SERVER['HTTP_REFERER']);
        }

        $aboutMe = new AboutMeDAO();
        $aboutMe = $aboutMe->getAboutMe();
        $data = ['aboutMe' => $aboutMe];

        $this->render('login.html.twig', $data);
    }

    public function authenticate() {
        if (empty($this->post)) {
            $this->session['flash-error'] = "Aucune donnée reçu !";
            $this->redirect('/connexion');
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
            $this->session['flash-error'] = "Aucun compte ne correspond à cette adresse email.";
            $this->redirect('/connexion');
        }

        if (!password_verify($this->post['password'], $user->getPassword())) {
             $this->session['flash-error'] = "Mot de passe incorrect !";
             $this->redirect('/connexion');
        }

        if ($user->getIsDeactivated()) {
            $this->session['flash-error'] = "Ce compte à été désactivé.";
            $this->redirect('/connexion');
        }

        $this->session['email'] = $user->getEmail();
        $this->session['pseudo'] = $user->getPseudo();
        $this->session['role'] = $user->getRole();
        $this->session['profilPicture'] = $user->getProfilPicture();

        ($this->session['role'] === 'ROLE_ADMIN') ? $this->redirect('/admin/tableau-de-bord') : $this->redirect('/');
    }

    public function logout() {
        $this->session['flash-success'] = "Vous êtes déconnecté.";
        unset($this->session['email']);
        unset($this->session['pseudo']);
        unset($this->session['role']);
        unset($this->session['profilPicture']);
        $this->redirect('/');
    }
}