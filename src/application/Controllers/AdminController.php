<?php


namespace App\Controllers;


use App\DAO\AboutMeDAO;
use App\DAO\CommentDAO;
use App\DAO\PostDAO;
use App\DAO\UserDAO;
use App\DTO\AboutMeDTO;
use App\DTO\PostDTO;
use App\Form\FormValidator;

class AdminController extends Controller
{
    public function index()
    {
        if (empty($_SESSION) || $_SESSION['role'] !== 'ROLE_ADMIN') {
            $this->session['flash-error'] = "Vous ne pouvez pas accéder à cette partie du site.";
            $this->redirect('/');
        }

        $postDAO = new PostDAO();
        $filtersPost = ['is_archived' => 0];
        $posts = $postDAO->getAll($filtersPost,5);

        $userDAO = new UserDAO();
        $filtersUser = ['is_deactivated' => 0];
        $users = $userDAO->getAll($filtersUser, 5);

        $commentDAO = new CommentDAO();
        $filtersComment = ['status' => 'NULL'];
        $comments = $commentDAO->getAll($filtersComment, 5);

        $data = ['posts' => $posts, 'users' => $users, 'comments' => $comments];

        $this->render('admin/dashboard.html.twig', $data);
    }

    public function listPosts()
    {
        if (empty($_SESSION)|| $_SESSION['role'] !== 'ROLE_ADMIN') {
            $this->session['flash-error'] = "Vous ne pouvez pas accéder à cette partie du site.";
            $this->redirect('/');
        }

        $data = [];

        $postDAO = new PostDAO();
        $filtersPosts = ['is_archived' => 0];
        $posts = $postDAO->getAll($filtersPosts);

        if ($posts) {
            $data['posts'] = $posts;
        }

        $filtersPostsArchived = ['is_archived' => 1];
        $postsArchived = $postDAO->getAll($filtersPostsArchived);

        if ($postsArchived) {
            $data['postsArchived'] = $postsArchived;
        }

        $this->render('admin/posts.html.twig', $data);
    }

    public function addPost()
    {
        if (empty($_SESSION)|| $_SESSION['role'] !== 'ROLE_ADMIN') {
            $this->session['flash-error'] = "Vous ne pouvez pas accéder à cette partie du site.";
            $this->redirect('/');
        }

        $this->render('admin/add_post.html.twig');
    }

    public function newPost()
    {
        if (empty($_SESSION)|| $_SESSION['role'] !== 'ROLE_ADMIN') {
            $this->session['flash-error'] = "Vous ne pouvez pas accéder à cette partie du site.";
            $this->redirect('/');
        }

        if (empty($this->post)) {
            $this->session['flash-error'] = "Aucune donnée reçu !";
            $this->redirect('/admin/articles/nouveau');
        }

        if(!empty($_FILES)) {
            unset($_FILES['files']);
            foreach ($_FILES as $inputName => $file) {
                $this->post[$inputName] = $file;
            }
        }

        $form = new FormValidator();
        $rules = [
            [
                'fieldName' => 'title',
                'type' => 'string',
                'minLength' => 3,
                'maxLength' => 50,
                'required' => true,
            ],
            [
                'fieldName' => 'subtitle',
                'type' => 'string',
                'minLength' => 3,
                'maxLength' => 255,
                'required' => false,
            ],
            [
                'fieldName' => 'resume',
                'type' => 'string',
                'minLength' => 5,
                'maxLength' => 255,
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
                'fieldName' => 'content',
                'type' => 'string',
                'minLength' => 20,
                'maxLength' => 4000,
                'required' => true,
            ]
        ];

        if (!empty($form->validate($rules, $this->post))) {
            $this->session['form-errors'] = $form->getErrors();
            $this->session['form-inputs'] = $this->post;
            $this->redirect('/admin/articles/nouveau');
        }

        if (!empty($this->post['picture']['tmp_name'])) {
            move_uploaded_file($this->post['picture']['tmp_name'], __DIR__.'/../../assets/img/post/' . basename($this->post['picture']['name']));
        }
        $this->post['picture'] = $this->post['picture']['name'];

        $postDTO = new PostDTO($this->post);
        $postDTO->setSlug($this->slugify($postDTO->getTitle()));
        $subtitle = $postDTO->getSubtitle() ? $postDTO->getSubtitle() : null;
        $postDTO->setSubtitle($subtitle);
        $resume = $postDTO->getResume() ? $postDTO->getResume() : null;
        $postDTO->setResume($resume);
        $picture = $postDTO->getPicture() ? $postDTO->getPicture() : null;
        $postDTO->setPicture($picture);

        $postDAO = new PostDAO();
        if (!empty($postDAO->getPostBySlug($postDTO->getSlug()))) {
            $this->session['form-errors'] = ['title' => ['Un article existe déjà avec ce titre.']];
            $this->session['form-inputs'] = $this->post;
            $this->redirect('/admin/articles/nouveau');
        }

        $result = $postDAO->save($postDTO);
        if (!$result) {
            $this->session['error'] = "Erreur ! Veuillez vérifier les champs du formulaire.";
            $this->session['flash-error'] = "Erreur interne ! Aucune modification n'a pu être enregistrée.";
            $this->redirect('admin/articles/nouveau');
        }
        $this->session['flash-success'] = "Modifications enregistrées.";
        $this->redirect('/admin/articles');
    }

    public function editPost(int $postId)
    {
        if (empty($_SESSION)|| $_SESSION['role'] !== 'ROLE_ADMIN') {
            $this->session['flash-error'] = "Vous ne pouvez pas accéder à cette partie du site.";
            $this->redirect('/');
        }

        $postDAO = new PostDAO();
        $postDTO = $postDAO->getPostById($postId);

        if (empty($postDTO)) {
            $this->session['flash-error'] = 'Erreur interne, article non trouvé.';
            $this->redirect('/admin/articles');
        }

        $data = ['post' => $postDTO];

        $this->render('admin/edit_post.html.twig', $data);
    }

    public function updatePost(int $postId)
    {
        if (empty($_SESSION)|| $_SESSION['role'] !== 'ROLE_ADMIN') {
            $this->session['flash-error'] = "Vous ne pouvez pas accéder à cette partie du site.";
            $this->redirect('/');
        }

        if (empty($this->post)) {
            $this->session['flash-error'] = "Aucune donnée reçu !";
            $this->redirect('/admin/article/'.$postId);
        }

        if(!empty($_FILES)) {
            foreach ($_FILES as $inputName => $file) {
                $this->post[$inputName] = $file;
            }
        }

        $form = new FormValidator();
        $rules = [
            [
                'fieldName' => 'title',
                'type' => 'string',
                'minLength' => 3,
                'maxLength' => 50,
                'required' => true,
            ],
            [
                'fieldName' => 'subtitle',
                'type' => 'string',
                'minLength' => 3,
                'maxLength' => 255,
                'required' => false,
            ],
            [
                'fieldName' => 'resume',
                'type' => 'string',
                'minLength' => 5,
                'maxLength' => 255,
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
                'fieldName' => 'content',
                'type' => 'string',
                'minLength' => 20,
                'maxLength' => 4000,
                'required' => true,
            ]
        ];

        if (!empty($form->validate($rules, $this->post))) {
            $this->session['form-errors'] = $form->getErrors();
            $this->session['form-inputs'] = $this->post;
            $this->redirect('/admin/article/'.$postId);
        }

        $postDAO = new PostDAO();
        $postDTO = $postDAO->getPostById($postId);

        if (empty($postDTO)) {
            $this->session['flash-error'] = 'Erreur interne, article non trouvé.';
            $this->redirect('/admin/articles');
        }

        $this->post['slug'] = $this->slugify($this->post['title']);
        if (!empty($this->post['picture']['tmp_name'])) {
            move_uploaded_file($this->post['picture']['tmp_name'], __DIR__.'/../../assets/img/post/' . basename($this->post['picture']['name']));
            if (!empty($postDTO->getPicture())) {
                unlink(__DIR__.'/../../assets/img/post/' . $postDTO->getPicture());
            }
            $this->post['picture'] = $this->post['picture']['name'];
        } else {
            unset($this->post['picture']);
        }

        foreach ($this->post as $key => $value) {
            if (empty($value)) {
                $this->post[$key] = null;
            }
        }

        $postDTO->hydrate($this->post);

        if (!empty($postDAO->getPostBySlug($postDTO->getSlug())) && $postDAO->getPostBySlug($postDTO->getSlug())->getId() !== $postDTO->getId()) {
            $this->session['form-errors'] = ['title' => ['Un article existe déjà avec ce titre.']];
            $this->session['form-inputs'] = $this->post;
            $this->redirect('/admin/article/'.$postId);
        }

        $post = $postDAO->save($postDTO);
        if (!$post) {
            $this->session['flash-error'] = "Erreur interne ! Aucune modification n'a pu être enregistrée.";
            $this->redirect('/admin/article/'.$postId);
        }
        $this->session['flash-success'] = "Modifications enregistrées.";
        $this->redirect('/admin/articles');
    }

    public function listUsers()
    {
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

    public function editUser(int $userId)
    {
        if (empty($_SESSION)|| $_SESSION['role'] !== 'ROLE_ADMIN') {
            $this->session['flash-error'] = "Vous ne pouvez pas accéder à cette partie du site.";
            $this->redirect('/');
        }

        $userDAO = new UserDAO();
        $userDTO = $userDAO->getUserById($userId);

        if (empty($userDTO)) {
            $this->session['flash-error'] = 'Erreur interne, utilisateur non trouvé.';
            $this->redirect('/admin/utilisateurs');
        }

        $data = ['user' => $userDTO];

        $this->render('admin/edit_user.html.twig', $data);
    }

    public function updateUser(int $userId)
    {
        if (empty($_SESSION)|| $_SESSION['role'] !== 'ROLE_ADMIN') {
            $this->session['flash-error'] = "Vous ne pouvez pas accéder à cette partie du site.";
            $this->redirect('/');
        }

        if (empty($this->post)) {
            $this->session['flash-error'] = "Aucune donnée reçu !";
            $this->redirect('/admin/utilisateur/'.$userId);
        }

        if(!empty($_FILES)) {
            foreach ($_FILES as $inputName => $file) {
                $this->post[$inputName] = $file;
            }
        }

        $form = new FormValidator();
        $rules = [
            [
                'fieldName' => 'email',
                'type' => 'email',
                'minLength' => 5,
                'maxLength' => 70,
                'required' => true,
            ],
            [
                'fieldName' => 'pseudo',
                'type' => 'string',
                'minLength' => 3,
                'maxLength' => 255,
                'required' => true,
            ],
            [
                'fieldName' => 'password',
                'type' => 'string',
                'minLength' => 8,
                'maxLength' => 255,
                'required' => false,
            ],
            [
                'fieldName' => 'profil_picture',
                'type' => 'file',
                'extension' => ['image/png', 'image/jpg', 'image/jpeg'],
                'size' => 2097152,
                'required' => false,
            ]
        ];

        if (!empty($form->validate($rules, $this->post))) {
            $this->session['form-errors'] = $form->getErrors();
            $this->session['form-inputs'] = $this->post;
            $this->redirect('/admin/utilisateur/'.$userId);
        }

        $userDAO = new UserDAO();
        $userDTO = $userDAO->getUserById($userId);

        if (empty($userDTO)) {
            $this->session['flash-error'] = 'Erreur interne, utilisateur non trouvé.';
            $this->redirect('/admin/utilisateurs');
        }

        if (!empty($this->post['profil_picture']['tmp_name'])) {
            move_uploaded_file($this->post['profil_picture']['tmp_name'], __DIR__.'/../../assets/img/user/profil_picture/' . basename($this->post['profil_picture']['name']));
            if (!empty($userDTO->getProfilPicture())) {
                unlink(__DIR__.'/../../assets/img/user/profil_picture/' . $userDTO->getProfilPicture());
            }
            $this->post['profil_picture'] = $this->post['profil_picture']['name'];
        } else {
            unset($this->post['profil_picture']);
        }

        foreach ($this->post as $key => $value) {
            if (empty($value)) {
                $this->post[$key] = null;
            }
        }

        if (!empty($this->post['password'])) {
            $this->post['password'] = password_hash($this->post['password'], PASSWORD_BCRYPT);
        } else {
            unset($this->post['password']);
        }

        $userDTO->hydrate($this->post);

        if (!empty($userDAO->getUserByEmail($userDTO->getEmail())) && $userDAO->getUserByEmail($userDTO->getEmail())->getId() !== $userDTO->getId()) {
            $this->session['form-errors'] = ['email' => ['Un utilisateur avec cette adresse email existe déjà !']];
            $this->session['form-inputs'] = $this->post;
            $this->redirect('/admin/utilisateur/'.$userId);
        }

        if (!empty($userDAO->getUserByPseudo($userDTO->getPseudo())) && $userDAO->getUserByPseudo($userDTO->getPseudo())->getId() !== $userDTO->getId()) {
            $this->session['form-errors'] = ['pseudo' => ['Un utilisateur avec ce pseudo existe déjà !']];
            $this->session['form-inputs'] = $this->post;
            $this->redirect('/admin/utilisateur/'.$userId);
        }

        $user = $userDAO->save($userDTO);
        if (!$user) {
            $this->session['flash-error'] = "Erreur interne ! Aucune modification n'a pu être enregistrée.";
            $this->redirect('/admin/utilisateur/'.$userId);
        }
        $this->session['flash-success'] = "Modifications enregistrées.";
        $this->redirect('/admin/utilisateurs');
    }

    public function listComments()
    {
        if (empty($_SESSION)|| $_SESSION['role'] !== 'ROLE_ADMIN') {
            $this->session['flash-error'] = "Vous ne pouvez pas accéder à cette partie du site.";
            $this->redirect('/');
        }

        $data = [];

        $comments = new CommentDAO();
        $filters = ['status' => 'NULL'];
        $comments = $comments->getAll($filters);

        if ($comments) {
            $data['comments'] = $comments;
        }

        $this->render('admin/comments.html.twig', $data);
    }

    public function aboutMe()
    {
        if (empty($_SESSION)|| $_SESSION['role'] !== 'ROLE_ADMIN') {
            $this->session['flash-error'] = "Vous ne pouvez pas accéder à cette partie du site.";
            $this->redirect('/');
        }

        $data = [];

        $aboutMe = new AboutMeDAO();
        $aboutMe = $aboutMe->getAboutMe();
        $data['aboutMe'] = $aboutMe;

        $this->render('admin/edit_aboutme.html.twig', $data);
    }

    public function editAboutMe()
    {
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
                'minLength' => 2,
                'maxLength' => 25,
                'required' => true,
            ],
            [
                'fieldName' => 'lastname',
                'type' => 'string',
                'minLength' => 2,
                'maxLength' => 25,
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
            $this->session['form-errors'] = $form->getErrors();
            $this->session['form-inputs'] = $this->post;
            $this->redirect('/admin/a-propos');
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