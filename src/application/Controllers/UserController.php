<?php


namespace App\Controllers;


use App\DAO\AboutMeDAO;
use App\DAO\UserDAO;
use App\DTO\UserDTO;
use App\Form\FormValidator;


class UserController extends Controller
{
    // Register user view
    public function register()
    {
        // Get AboutMe infos
        $aboutMe = new AboutMeDAO();
        $aboutMe = $aboutMe->getAboutMe();
        $data = ['aboutMe' => $aboutMe];

        // show register user view
        $this->render('register.html.twig', $data);
    }

    // Create user
    public function addUser()
    {
        // If no data set error and redirect
        if (empty($this->post)) {
            $this->session['flash-error'] = "Aucune donnée reçue !";
            $this->redirect('/inscription');
        }

        // Create form validation rules
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

        // Check if form data send is valid
        if (!empty($form->validate($rules, $_POST))) {
            // Set error and redirect
            $this->session['form-errors'] = $form->getErrors();
            $this->session['form-inputs'] = $this->post;
            $this->redirect('/inscription');
        }

        // Set user object with post data
        $userDTO = new UserDTO($this->post);
        // Hash user password
        $userDTO->setPassword(password_hash($this->post['password'], PASSWORD_BCRYPT));

        $user = new UserDAO();

        // Checks if an user with this email doesn't already exists
        if (!empty($user->getUserByEmail($userDTO->getEmail()))) {
            // Set error
            $this->session['flash-error'] = "Un compte avec cette adresse email existe déjà !";
            // Set data send in inputs
            $this->session['form-inputs'] = $this->post;
            // Redirects
            $this->redirect('/inscription');
        }

        // Checks if an user with this pseudo doesn't already exists
        if (!empty($user->getUserByPseudo($userDTO->getPseudo()))) {
            // Set error
            $this->session['flash-error'] = "Un compte avec ce pseudo existe déjà !";
            // Set data send in inputs
            $this->session['form-inputs'] = $this->post;
            // Redirects
            $this->redirect('/inscription');
        }

        // Create user
        $user = $user->save($userDTO);

        // If user doesn't created add error and redirect
        if (!$user) {
            $this->session['flash-error'] = "Erreur interne, votre compte n'a pu être créée";
            $this->redirect('/inscription');
        }

        // Create data session
        $this->session['email'] = $userDTO->getEmail();
        $this->session['pseudo'] = $userDTO->getPseudo();
        $this->session['role'] = $userDTO->getRole();
        // Set success message
        $this->session['flash-success'] = "Vous êtes désormais inscrit. Bienvenue !";
        // Redirect
        $this->redirect('/');
    }

    // Login view
    public function login()
    {
        // If user already logged set error and redirect
        if (isset($this->session['email']) && isset($this->session['role']) && isset($this->session['pseudo'])) {
            // Set error
            $this->session['flash-error'] = "Vous êtes déjà connecté !";
            // Redirect
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }

        // Get AboutMe infos
        $aboutMe = new AboutMeDAO();
        $aboutMe = $aboutMe->getAboutMe();
        $data = ['aboutMe' => $aboutMe];

        // Show login view
        $this->render('login.html.twig', $data);
    }

    // Authenticate the user
    public function authenticate()
    {
        // If no data set error
        if (empty($this->post)) {
            // Set error
            $this->session['flash-error'] = "Aucune donnée reçu !";
            // Redirect
            $this->redirect('/connexion');
        }

        // Set validation form rules
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

        // Checks if valid form
        if (!empty($form->validate($rules, $_POST))) {
            // Set errors
            $this->session['form-errors'] = $form->getErrors();
            // Set data form inputs
            $this->session['form-inputs'] = $this->post;
            // Redirect
            $this->redirect('/connexion');
        }

        // Get user with it's email
        $user = new UserDAO();
        $user = $user->getUserByEmail($this->post['email']);

        // Checks if user is retrieved
        if (empty($user)) {
            // Set error
            $this->session['flash-error'] = "Aucun compte ne correspond à cette adresse email.";
            // Redirect
            $this->redirect('/connexion');
        }

        // Check if user password is correct
        if (!password_verify($this->post['password'], $user->getPassword())) {
            // Set errors
             $this->session['flash-error'] = "Mot de passe incorrect !";
             // Set data form inpits
             $this->session['form-inputs'] = $this->post;
             // Redirect
             $this->redirect('/connexion');
        }

        // Check if user is deactivate
        if ($user->getIsDeactivated()) {
            // Set an error
            $this->session['flash-error'] = "Ce compte à été désactivé.";
            // Add data form inputs
            $this->session['form-inputs'] = $this->post;
            // Redirect
            $this->redirect('/connexion');
        }

        // Set session data
        $this->session['email'] = $user->getEmail();
        $this->session['pseudo'] = $user->getPseudo();
        $this->session['role'] = $user->getRole();
        $this->session['profilPicture'] = $user->getProfilPicture();

        // Redirect
        ($this->session['role'] === 'ROLE_ADMIN') ? $this->redirect('/admin/tableau-de-bord') : $this->redirect('/');
    }

    // Logout user
    public function logout()
    {
        // Unset data from session
        unset($this->session['email']);
        unset($this->session['pseudo']);
        unset($this->session['role']);
        unset($this->session['profilPicture']);
        // Set success message
        $this->session['flash-success'] = "Vous êtes déconnecté.";
        // Redirect
        $this->redirect('/');
    }
}