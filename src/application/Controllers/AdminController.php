<?php


namespace App\Controllers;


use App\DAO\AboutMeDAO;
use App\DAO\CommentDAO;
use App\DAO\PostDAO;
use App\DAO\UserDAO;
use App\DTO\AboutMeDTO;
use App\Form\FormValidator;

class AdminController extends Controller {

    public function index()
    {
        if (empty($_SESSION) || $_SESSION['role'] !== 'ROLE_ADMIN') {
            $this->session['flash-error'] = "Vous ne pouvez pas accéder à cette partie du site.";
            $this->redirect('/');
        }

        $postDAO = new PostDAO();
        $posts = $postDAO->getAll(5);

        $userDAO = new UserDAO();
        $filtersUser = ['is_deactivated' => 0];
        $users = $userDAO->getAll($filtersUser, 5);

        $commentDAO = new CommentDAO();
        $filtersComment = ['status' => 'NULL'];
        $comments = $commentDAO->getAll($filtersComment, 5);

        $data['posts'] = $posts;
        $data['users'] = $users;
        $data['comments'] = $comments;

        $this->render('admin/dashboard.html.twig', $data);
    }

    public function listPosts() {
        if (empty($_SESSION)|| $_SESSION['role'] !== 'ROLE_ADMIN') {
            $this->session['flash-error'] = "Vous ne pouvez pas accéder à cette partie du site.";
            $this->redirect('/');
        }

        $data = [];

        $postDAO = new PostDAO();
        $posts = $postDAO->getAll();

        if ($posts) {
            $data['posts'] = $posts;
        }

        $postsArchived = $postDAO->getAllArchived();

        if ($postsArchived) {
            $data['postsArchived'] = $postsArchived;
        }

        $this->render('admin/posts.html.twig', $data);
    }

    public function listUsers() {
        if (empty($_SESSION)|| $_SESSION['role'] !== 'ROLE_ADMIN') {
            $this->session['flash-error'] = "Vous ne pouvez pas accéder à cette partie du site.";
            $this->redirect('/');
        }

        $data = [];

        $userDAO = new UserDAO();
        $filters = ['is_deactivated' => 0];
        $users = $userDAO->getAll($filters);

        if (!empty($users)) {
            $data['users'] = $users;
        }

        $filters = ['is_deactivated' => 1];
        $usersDeactivated = $userDAO->getAll($filters);

        if (!empty($usersDeactivated)) {
            $data['usersDeactivated'] = $usersDeactivated;
        }

        $this->render('admin/users.html.twig', $data);
    }

    public function listComments() {
        if (empty($_SESSION)|| $_SESSION['role'] !== 'ROLE_ADMIN') {
            $this->session['flash-error'] = "Vous ne pouvez pas accéder à cette partie du site.";
            $this->redirect('/');
        }

        $data = [];

        $comments = new CommentDAO();
        $filters = ['status' => 'NULL', ''];
        $comments = $comments->getAll($filters);

        if ($comments) {
            $data['comments'] = $comments;
        }

        $this->render('admin/comments.html.twig', $data);
    }

    public function aboutMe() {
        if (empty($_SESSION)|| $_SESSION['role'] !== 'ROLE_ADMIN') {
            $this->session['flash-error'] = "Vous ne pouvez pas accéder à cette partie du site.";
            $this->redirect('/');
        }

        $data = [];

        $aboutMe = new AboutMeDAO();
        $aboutMe = $aboutMe->getAboutMe();
        $data['aboutMe'] = $aboutMe;

        $this->render('admin/aboutme.html.twig', $data);
    }

    public function editAboutMe() {
        if (empty($_SESSION)|| $_SESSION['role'] !== 'ROLE_ADMIN') {
            $this->session['flash-error'] = "Vous ne pouvez pas accéder à cette partie du site.";
            $this->redirect('/');
        }

        if (empty($this->post)) {
            $this->session['flash-error'] = "Aucune donnée reçu !";
            $this->redirect('/admin/a-propos');
        }

        if(!empty($_FILES)) {
            foreach ($_FILES as $inputName => $file) {
                $this->post[$inputName] = $file;
            }
        }

        $form = new FormValidator();
        $rules = [
            [
                'fieldName' => 'firstname',
                'type' => 'string',
                'minLength' => 3,
                'maxLength' => 255,
                'required' => true,
            ],
            [
                'fieldName' => 'lastname',
                'type' => 'string',
                'minLength' => 3,
                'maxLength' => 255,
                'required' => true,
            ],
            [
                'fieldName' => 'slogan',
                'type' => 'string',
                'minLength' => 5,
                'maxLength' => 255,
                'required' => true,
            ],
            [
                'fieldName' => 'bio',
                'type' => 'string',
                'minLength' => 5,
                'maxLength' => 255,
                'required' => true,
            ],
            [
                'fieldName' => 'profil_picture',
                'type' => 'file',
                'extension' => ['image/png', 'image/jpg', 'image/jpeg'],
                'size' => 2097152,
                'required' => false,
            ],
            [
                'fieldName' => 'cv_pdf',
                'type' => 'file',
                'extension' => ['application/pdf'],
                'size' => 2097152,
                'required' => false,
            ],
            [
                'fieldName' => 'picture',
                'type' => 'file',
                'extension' => ['image/png', 'image/jpg', 'image/jpeg'],
                'size' => 2097152,
                'required' => false,
            ],
            [
                'fieldName' => 'twitter_link',
                'type' => 'string',
                'minLength' => 20,
                'maxLength' => 255,
                'required' => true,
            ],
            [
                'fieldName' => 'linkedin_link',
                'type' => 'string',
                'minLength' => 20,
                'maxLength' => 255,
                'required' => true,
            ],
            [
                'fieldName' => 'github_link',
                'type' => 'string',
                'minLength' => 20,
                'maxLength' => 255,
                'required' => true,
            ]
        ];

        if (!empty($form->validate($rules, $this->post))) {
            var_dump($form->getErrors());
            echo "Formulaire invalide";
            return;
            //@TODO Display an error - invalid or missing data required
        }

        $aboutMeDAO = new AboutMeDAO();
        $aboutMeDTO = $aboutMeDAO->getAboutMe();

        $aboutMe = new AboutMeDTO();
        $aboutMe = $aboutMe->setId($aboutMeDTO->getId());

        if ($this->post['firstname'] !== $aboutMeDTO->getFirstname()) {
            $aboutMe->setFirstname($this->post['firstname']);
        } else {
            $aboutMe->setFirstname($aboutMeDTO->getFirstname());
        }

        if ($this->post['lastname'] !== $aboutMeDTO->getLastname()) {
            $aboutMe->setLastname($this->post['lastname']);
        } else {
            $aboutMe->setLastname($aboutMeDTO->getLastname());
        }

        if ($this->post['slogan'] !== $aboutMeDTO->getSlogan()) {
            $aboutMe->setSlogan($this->post['slogan']);
        } else {
            $aboutMe->setSlogan($aboutMeDTO->getSlogan());
        }

        if ($this->post['bio'] !== $aboutMeDTO->getBio()) {
            $aboutMe->setBio($this->post['bio']);
        } else {
            $aboutMe->setBio($aboutMeDTO->getBio());
        }

        if (!empty($this->post['profil_picture']['name'])) {
            move_uploaded_file($this->post['profil_picture']['tmp_name'], __DIR__.'/../../assets/aboutme/' . basename($this->post['profil_picture']['name']));
            $aboutMe->setProfilPicture($this->post['profil_picture']['name']);
            unlink(__DIR__.'/../../assets/aboutme/' .$aboutMeDTO->getProfilPicture());
        } else {
            $aboutMe->setProfilPicture($aboutMeDTO->getProfilPicture());
        }

        if (!empty($this->post['cv_pdf']['name'])) {
            move_uploaded_file($this->post['cv_pdf']['tmp_name'], __DIR__.'/../../assets/aboutme/' . basename($this->post['cv_pdf']['name']));
            $aboutMe->setCvPdf($this->post['cv_pdf']['name']);
            unlink(__DIR__.'/../../assets/aboutme/' .$aboutMeDTO->getCvPdf());
        } else {
            $aboutMe->setCvPdf($aboutMeDTO->getCvPdf());
        }

        if (!empty($this->post['picture']['name'])) {
            move_uploaded_file($this->post['picture']['tmp_name'], __DIR__.'/../../assets/aboutme/' . basename($this->post['picture']['name']));
            $aboutMe->setPicture($this->post['picture']['name']);
            unlink(__DIR__.'/../../assets/aboutme/' .$aboutMeDTO->getPicture());
        } else {
            $aboutMe->setPicture($aboutMeDTO->getPicture());
        }

        if ($this->post['twitter_link'] !== $aboutMeDTO->getTwitterLink()) {
            $aboutMe->setTwitterLink($this->post['twitter_link']);
        } else {
            $aboutMe->setTwitterLink($aboutMeDTO->getTwitterLink());
        }

        if ($this->post['linkedin_link'] !== $aboutMeDTO->getLinkedinLink()) {
            $aboutMe->setLinkedinLink($this->post['linkedin_link']);
        } else {
            $aboutMe->setLinkedinLink($aboutMeDTO->getLinkedinLink());
        }

        if ($this->post['github_link'] !== $aboutMeDTO->getGithubLink()) {
            $aboutMe->setGithubLink($this->post['github_link']);
        } else {
            $aboutMe->setGithubLink($aboutMeDTO->getGithubLink());
        }

        $aboutMe = $aboutMeDAO->save($aboutMe);
        if (!$aboutMe) {
            $this->session['flash-error'] = "Erreur interne ! Aucune modification n'a pu être enregistrée.";
            $this->redirect('/admin/a-propos');
        }
        $this->session['flash-success'] = "Modifications enregistrées.";
        $this->redirect('/admin/a-propos');
    }
}