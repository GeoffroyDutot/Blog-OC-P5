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
        if (empty($_SESSION)|| $_SESSION['role'] !== 'ROLE_ADMIN') {
            $this->redirect('/');
            //@TODO Display an error - User doesn't have the right access to admin
        }

        $posts = new PostDAO();
        $posts = $posts->getAll(5);

        $users = new UserDAO();
        $users = $users->getAll(5);

        $comments = new CommentDAO();
        $comments = $comments->getAllCommentsUnvalidated(5);

        $data['posts'] = $posts;
        $data['users'] = $users;
        $data['comments'] = $comments;

        $this->render('admin/dashboard.html.twig', $data);
    }

    public function listPosts() {
        if (empty($_SESSION)|| $_SESSION['role'] !== 'ROLE_ADMIN') {
            $this->redirect('/');
            //@TODO Display an error - User doesn't have the right access to admin
        }

        $data = [];

        $posts = new PostDAO();
        $posts = $posts->getAll();

        if ($posts) {
            $data['posts'] = $posts;
        }

        $this->render('admin/posts.html.twig', $data);
    }

    public function listUsers() {
        if (empty($_SESSION)|| $_SESSION['role'] !== 'ROLE_ADMIN') {
            $this->redirect('/');
            //@TODO Display an error - User doesn't have the right access to admin
        }

        $data = [];

        $users = new UserDAO();
        $users = $users->getAll();

        if ($users) {
            $data['users'] = $users;
        }

        $this->render('admin/users.html.twig', $data);
    }

    public function listComments() {
        if (empty($_SESSION)|| $_SESSION['role'] !== 'ROLE_ADMIN') {
            $this->redirect('/');
            //@TODO Display an error - User doesn't have the right access to admin
        }

        $data = [];

        $comments = new CommentDAO();
        $comments = $comments->getAllCommentsUnvalidated();

        if ($comments) {
            $data['comments'] = $comments;
        }

        $this->render('admin/comments.html.twig', $data);
    }

    public function aboutMe() {
        if (empty($_SESSION)|| $_SESSION['role'] !== 'ROLE_ADMIN') {
            $this->redirect('/');
            //@TODO Display an error - User doesn't have the right access to admin
        }

        $data = [];

        $aboutMe = new AboutMeDAO();
        $aboutMe = $aboutMe->getAboutMe();
        $data['aboutMe'] = $aboutMe;

        $this->render('admin/aboutme.html.twig', $data);
    }

    public function editAboutMe() {
        if (empty($_SESSION)|| $_SESSION['role'] !== 'ROLE_ADMIN') {
            $this->redirect('/');
            //@TODO Display an error - User doesn't have the right access to admin
        }

        if (empty($this->post)) {
            $this->redirect('/admin/a-propos');
            //@TODO Display an error - empty data
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
            $this->redirect('/admin/a-propos');
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

        //@TODO Delete old files
        if (!empty($_FILES['profil_picture']['name'])) {
            if ($_FILES['profil_picture']['error'] === 0) {
                if ($_FILES['profil_picture']['size'] <= 1000000) {
                    $fileData = pathinfo($_FILES['profil_picture']['name']);
                    $fileExtension = $fileData['extension'];
                    $allowedExtensions = ['jpg', 'jpeg', 'png'];
                    if (in_array($fileExtension, $allowedExtensions)) {
                        move_uploaded_file($_FILES['profil_picture']['tmp_name'], __DIR__.'/../../assets/aboutme/' . basename($_FILES['profil_picture']['name']));
                        $aboutMe->setProfilPicture($_FILES['profil_picture']['name']);
                        unlink(__DIR__.'/../../assets/aboutme/' .$aboutMeDTO->getProfilPicture());
                    } else {
                        $this->redirect('/admin/a-propos');
                        //@TODO Display Error wrong extension file
                    }
                } else {
                    $this->redirect('/admin/a-propos');
                    //@TODO Display Error File too big
                }
            } elseif($_FILES['profil_picture']['error'] !== 4) {
                $this->redirect('/admin/a-propos');
                //@TODO Display an Internal Error
            }
        } else {
            $aboutMe->setProfilPicture($aboutMeDTO->getProfilPicture());
        }

        if (!empty($_FILES['cv_pdf']['name'])) {
            if ($_FILES['cv_pdf']['error'] === 0) {
                if ($_FILES['cv_pdf']['size'] <= 1000000) {
                    $fileData = pathinfo($_FILES['cv_pdf']['name']);
                    $fileExtension = $fileData['extension'];
                    $allowedExtensions = ['pdf'];
                    if (in_array($fileExtension, $allowedExtensions)) {
                        move_uploaded_file($_FILES['cv_pdf']['tmp_name'], __DIR__.'/../../assets/aboutme/' . basename($_FILES['cv_pdf']['name']));
                        $aboutMe->setCvPdf($_FILES['cv_pdf']['name']);
                        unlink(__DIR__.'/../../assets/aboutme/' .$aboutMeDTO->getCvPdf());
                    } else {
                        $this->redirect('/admin/a-propos');
                        //@TODO Display Error wrong extension file
                    }
                } else {
                    $this->redirect('/admin/a-propos');
                    //@TODO Display Error File too big
                }
            } elseif($_FILES['profil_picture']['error'] !== 4) {
                $this->redirect('/admin/a-propos');
                //@TODO Display an Internal Error
            }
        } else {
            $aboutMe->setCvPdf($aboutMeDTO->getCvPdf());
        }

        if (!empty($_FILES['picture']['name'])) {
            if ($_FILES['picture']['error'] === 0) {
                if ($_FILES['picture']['size'] <= 1000000) {
                    $fileData = pathinfo($_FILES['picture']['name']);
                    $fileExtension = $fileData['extension'];
                    $allowedExtensions = ['jpg', 'jpeg', 'png'];
                    if (in_array($fileExtension, $allowedExtensions)) {
                        move_uploaded_file($_FILES['picture']['tmp_name'], __DIR__.'/../../assets/aboutme/' . basename($_FILES['picture']['name']));
                        $aboutMe->setCvPdf($_FILES['picture']['name']);
                        unlink(__DIR__.'/../../assets/aboutme/' .$aboutMeDTO->getPicture());
                    } else {
                        $this->redirect('/admin/a-propos');
                        //@TODO Display Error wrong extension file
                    }
                } else {
                    $this->redirect('/admin/a-propos');
                    //@TODO Display Error File too big
                }
            } elseif($_FILES['profil_picture']['error'] !== 4) {
                $this->redirect('/admin/a-propos');
                //@TODO Display an Internal Error
            }
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
        //@TODO Display success or error
        $this->redirect('/admin/a-propos');
    }
}