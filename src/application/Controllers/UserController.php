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
        if (!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['pseudo'] && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))) {
            $userDTO = new UserDTO();
            $userDTO->setEmail($_POST['email']);
            $userDTO->setPseudo($_POST['pseudo']);
            $userDTO->setPassword(password_hash($_POST['password'], PASSWORD_BCRYPT));
            //@TODO Check if email and pseudo doesn't already exists
            //$user = new UserDAO();
            //var_dump($user->getUserByEmail($_POST['email']));die();
            $user = (new UserDAO())->save($userDTO);
            if ($user) {
                $_SESSION['email'] = $userDTO->getEmail();
                $_SESSION['pseudo'] = $userDTO->getPseudo();
                var_dump($_SESSION);
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
        if (empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) && empty($_POST['password'])) {
            echo 'Erreur de connexion';
            return;
        }

        $user = new UserDAO();
        $user = $user->getUserByEmail($_POST['email']);
        if (!$user && $_POST['email'] !== $user->getEmail()) {
            echo 'Aucun utilisateur correspondant à cette adresse email à été trouvé';
            return;
        }

        if (!password_verify($_POST['password'], $user->getPassword())) {
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