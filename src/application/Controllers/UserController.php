<?php


namespace App\Controllers;


use App\DAO\AboutMeDAO;
use App\DAO\UserDAO;
use App\DTO\UserDTO;

class UserController extends Controller {
    public function register() {
        $data = [];

        $aboutMe = new AboutMeDAO();
        $aboutMe = $aboutMe->getAboutMe();
        $data['aboutMe'] = $aboutMe;

        $this->render('register.html.twig', $data);
    }

    public function addUser() {
        if (!empty($this->post['email']) && !empty($this->post['password']) && !empty($this->post['pseudo'] && filter_var($this->post['email'], FILTER_VALIDATE_EMAIL))) {
            $userDTO = new UserDTO();
            $userDTO->setEmail($this->post['email']);
            $userDTO->setPseudo($this->post['pseudo']);
            $userDTO->setPassword(password_hash($this->post['password'], PASSWORD_BCRYPT));
            //@TODO Check if email and pseudo doesn't already exists
            $user = (new UserDAO())->save($userDTO);
            if ($user) {
                $_SESSION['email'] = $userDTO->getEmail();
                $_SESSION['pseudo'] = $userDTO->getPseudo();
                echo 'Utilisateur ajouté !';
            }  else {
                echo 'Erreur, l\'utilisateut n\'as pas pu être ajouté';
            }
        } else {
            echo 'Erreur données invalides';
        }
    }

    public function login() {
        $data = [];

        if (isset($_SESSION['email']) && isset($_SESSION['role']) && isset($_SESSION['pseudo'])) {
            header('Location: /');
        }

        $aboutMe = new AboutMeDAO();
        $aboutMe = $aboutMe->getAboutMe();
        $data['aboutMe'] = $aboutMe;

        $this->render('login.html.twig', $data);
    }

    public function authenticate() {
        if (empty($this->post['email']) && !filter_var($this->post['email'], FILTER_VALIDATE_EMAIL) && empty($this->post['password'])) {
            echo 'Erreur de connexion';
            return;
        }

        $user = new UserDAO();
        $user = $user->getUserByEmail($this->post['email']);
        if (!$user && $this->post['email'] !== $user->getEmail()) {
            echo 'Aucun utilisateur correspondant à cette adresse email à été trouvé';
            return;
        }

        if (!password_verify($this->post['password'], $user->getPassword())) {
             echo 'Error incorrect password';
             return;
        }

        $_SESSION['email'] = $user->getEmail();
        $_SESSION['pseudo'] = $user->getPseudo();
        $_SESSION['role'] = $user->getRole();

        ($_SESSION['role'] === 'ROLE_ADMIN') ? header('Location: /admin/tableau-de-bord') : header('Location: /');
    }

    public function logout() {
    $_SESSION = [];
    session_destroy();
    echo 'vous êtes déconnecté';
    header('Location: /');
    }
}