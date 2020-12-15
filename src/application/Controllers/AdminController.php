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
    public function __construct()
    {
        parent::__construct();
        // Check if user logged and user is an admin
        if (empty($_SESSION) || $_SESSION['role'] !== 'ROLE_ADMIN') {
            // Set error message - user doesnt have rights
            $this->session['flash-error'] = "Vous ne pouvez pas accéder à cette partie du site.";
            // Return error
            $this->redirect('/');
        }
    }

    // Show admin Dashboard
    public function index()
    {
        // Get 5 lasts posts not archived
        $postDAO = new PostDAO();
        $filtersPost = ['is_archived' => 0];
        $posts = $postDAO->getAll($filtersPost,5);

        // Get 5 lasts users not deactivated
        $userDAO = new UserDAO();
        $filtersUser = ['is_deactivated' => 0];
        $users = $userDAO->getAll($filtersUser, 5);

        // Get 5 lasts comments submitted
        $commentDAO = new CommentDAO();
        $filtersComment = ['status' => 'NULL'];
        $comments = $commentDAO->getAll($filtersComment, 5);

        // Set data with posts, users and comments
        $data = ['posts' => $posts, 'users' => $users, 'comments' => $comments];

        // Show admin dashboard view
        $this->render('admin/dashboard.html.twig', $data);
    }

    // Show admin List manage posts
    public function listPosts()
    {
        // Get all Posts not archived
        $postDAO = new PostDAO();
        $filtersPosts = ['is_archived' => 0];
        $posts = $postDAO->getAll($filtersPosts);

        // Get all Posts archived
        $filtersPostsArchived = ['is_archived' => 1];
        $postsArchived = $postDAO->getAll($filtersPostsArchived);

        // Set data with posts and posts archived
        $data = ['posts' => $posts, 'postsArchived' => $postsArchived];

        // Show admin posts list view
        $this->render('admin/posts.html.twig', $data);
    }

    // Show add a new post view
    public function addPost()
    {
        // Show add new post form view
        $this->render('admin/add_post.html.twig');
    }

    // Create a new post
    public function newPost()
    {
        // Check if there is data required
        if (empty($this->post)) {
            // Set error - missing required data
            $this->session['flash-error'] = "Aucune donnée reçu !";
            // Redirect
            $this->redirect('/admin/articles/nouveau');
        }

        // If there is file uploaded add filenames in post data
        if(!empty($_FILES)) {
            unset($_FILES['files']);
            foreach ($_FILES as $inputName => $file) {
                $this->post[$inputName] = $file;
            }
        }

        // Set validation form rules
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

        // Checks if valid form
        if (!empty($form->validate($rules, $this->post))) {
            // Set form errors
            $this->session['form-errors'] = $form->getErrors();
            // Set data form inputs
            $this->session['form-inputs'] = $this->post;
            // Redirect
            $this->redirect('/admin/articles/nouveau');
        }

        $newPictureTmpName = $this->post['picture']['tmp_name'];
            // Set picture name
        $this->post['picture'] = !empty($newPictureTmpName) ? uniqid().'.png' : null;

        // Remove empty post's values
        foreach ($this->post as $key => $value) {
            if (empty($value)) {
                $this->post[$key] = null;
            }
        }

        // Create PostDTO
        $postDTO = new PostDTO($this->post);
        // Set post's slug from the title
        $postDTO->setSlug($this->slugify($postDTO->getTitle()));

        // Checks if a post with this slug already exists
        $postDAO = new PostDAO();
        if (!empty($postDAO->getPostBySlug($postDTO->getSlug()))) {
            // Set error - post with this title already exists
            $this->session['form-errors'] = ['title' => ['Un article existe déjà avec ce titre.']];
            // Set data inputs post
            $this->session['form-inputs'] = $this->post;
            // Redirect
            $this->redirect('/admin/articles/nouveau');
        }

        // Create post
        $result = $postDAO->save($postDTO);

        // Checks if post has been created
        if (!$result) {
            // Set error - Internal error
            $this->session['flash-error'] = "Erreur interne ! Aucune modification n'a pu être enregistrée.";
            // Redirect
            $this->redirect('admin/articles/nouveau');
        }

        // Upload file
        if (!empty($newPictureTmpName)) {
            move_uploaded_file($newPictureTmpName, __DIR__.'/../../assets/img/post/' . basename($postDTO->getPicture()));
        }

        // Set success message - post created
        $this->session['flash-success'] = "Modifications enregistrées.";
        // Redirect
        $this->redirect('/admin/articles');
    }

    // Show edit post view
    public function editPost(int $postId)
    {
        // Get post by its id
        $postDAO = new PostDAO();
        $postDTO = $postDAO->getPostById($postId);

        // Checks if post founded
        if (empty($postDTO)) {
            // Set error - post didnt found
            $this->session['flash-error'] = 'Erreur interne, article non trouvé.';
            // Redirect
            $this->redirect('/admin/articles');
        }

        // Show edit post view
        $this->render('admin/edit_post.html.twig', ['post' => $postDTO]);
    }

    // Update post
    public function updatePost(int $postId)
    {
        // Checks if not missing required data
        if (empty($this->post)) {
            // Set missing required data
            $this->session['flash-error'] = "Aucune donnée reçu !";
            // Redirect
            $this->redirect('/admin/article/'.$postId);
        }

        // If there is file uploaded add filenames in post data
        if(!empty($_FILES)) {
            unset($_FILES['files']);
            foreach ($_FILES as $inputName => $file) {
                $this->post[$inputName] = $file;
            }
        }

        // Set validation form rules
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

        // Checks if valid form
        if (!empty($form->validate($rules, $this->post))) {
            // Set form errors
            $this->session['form-errors'] = $form->getErrors();
            // Set data inputs from post
            $this->session['form-inputs'] = $this->post;
            // Redirect
            $this->redirect('/admin/article/'.$postId);
        }

        // Get post by it's id
        $postDAO = new PostDAO();
        $postDTO = $postDAO->getPostById($postId);

        // Checks if post has been founded
        if (empty($postDTO)) {
            // Set error - post didn't find
            $this->session['flash-error'] = 'Erreur interne, article non trouvé.';
            // Redirect
            $this->redirect('/admin/articles');
        }

        // If picture uploaded
        if (!empty($this->post['picture']['tmp_name'])) {
            // Set picture data
            $newPictureTmpName = $this->post['picture']['tmp_name'];
            // Get old Picture name
            $oldPicture = $postDTO->getPicture();
            // Set post picture name
            $this->post['picture'] = uniqid().'.png';
        } else {
            // Set post picture name with the old one
            $this->post['picture'] = $postDTO->getPicture();
        }

        // Remove empty post's values
        foreach ($this->post as $key => $value) {
            if (empty($value)) {
                $this->post[$key] = null;
            }
        }

        $postDTO->hydrate($this->post);
        $postDTO->setSlug($this->slugify($postDTO->getTitle()));

        // Checks if a post already exists with this slug
        if (!empty($postDAO->getPostBySlug($postDTO->getSlug())) && $postDAO->getPostBySlug($postDTO->getSlug())->getId() !== $postDTO->getId()) {
            // Set error - post with this title already exists
            $this->session['form-errors'] = ['title' => ['Un article existe déjà avec ce titre.']];
            // Set data inputs from post
            $this->session['form-inputs'] = $this->post;
            // Redirect
            $this->redirect('/admin/article/'.$postId);
        }

        // Update post
        $post = $postDAO->save($postDTO);

        // Checks if post has been updated
        if (!$post) {
            // Set error - error internal
            $this->session['flash-error'] = "Erreur interne ! Aucune modification n'a pu être enregistrée.";
            // Redirect
            $this->redirect('/admin/article/'.$postId);
        }

        // If there is a new picture
        if (!empty($newPictureTmpName)) {
            // Upload the picture
            move_uploaded_file($newPictureTmpName, __DIR__.'/../../assets/img/post/' . basename($postDTO->getPicture()));
            // If there is an old picture
            if (!empty($oldPicture)) {
                // Remove old picture
                unlink(__DIR__.'/../../assets/img/post/' . $oldPicture);
            }
        }

        // Set success message - Post updated
        $this->session['flash-success'] = "Modifications enregistrées.";
        // Redirect
        $this->redirect('/admin/articles');
    }

    // Show admin lists users
    public function listUsers()
    {
        // Get all users not deactivated
        $userDAO = new UserDAO();
        $filters = ['is_deactivated' => 0];
        $users = $userDAO->getAll($filters);

        // Get all users deactivated
        $filters = ['is_deactivated' => 1];
        $usersDeactivated = $userDAO->getAll($filters);

        // Show users lists
        $this->render('admin/users.html.twig', ['users' => $users, 'usersDeactivated' => $usersDeactivated]);
    }

    // Show admin edit user
    public function editUser(int $userId)
    {
        // Get User by it's id
        $userDAO = new UserDAO();
        $userDTO = $userDAO->getUserById($userId);

        // Checks if user found
        if (empty($userDTO)) {
            // Set error - user doesnt't find
            $this->session['flash-error'] = 'Erreur interne, utilisateur non trouvé.';
            // Redirect
            $this->redirect('/admin/utilisateurs');
        }

        // Show admin users lists view
        $this->render('admin/edit_user.html.twig', ['user' => $userDTO]);
    }

    // Update user
    public function updateUser(int $userId)
    {
        // Check if not missing required data
        if (empty($this->post)) {
            // Set error - missing required data
            $this->session['flash-error'] = "Aucune donnée reçu !";
            // Redirect
            $this->redirect('/admin/utilisateur/'.$userId);
        }

        // If there is file uploaded add filenames in post data
        if(!empty($_FILES)) {
            foreach ($_FILES as $inputName => $file) {
                $this->post[$inputName] = $file;
            }
        }

        // Add form validator rules
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

        // Check if form data is valid
        if (!empty($form->validate($rules, $this->post))) {
            $this->session['form-errors'] = $form->getErrors();
            $this->session['form-inputs'] = $this->post;
            $this->redirect('/admin/utilisateur/'.$userId);
        }

        // Get user by it's id
        $userDAO = new UserDAO();
        $userDTO = $userDAO->getUserById($userId);

        // Check if user found
        if (empty($userDTO)) {
            // Set error - user not found
            $this->session['flash-error'] = 'Erreur interne, utilisateur non trouvé.';
            // Redirect
            $this->redirect('/admin/utilisateurs');
        }

        // If picture uploaded
        if (!empty($this->post['profil_picture']['tmp_name'])) {
            // Set picture data
            $newPictureTmpName = $this->post['profil_picture']['tmp_name'];
            // Get old Picture name
            $oldPicture = $userDTO->getProfilPicture();
            // Set post picture name
            $this->post['profil_picture'] = uniqid().'.png';
        } else {
            // Set post picture name with the old one
            $this->post['profil_picture'] = $userDTO->getProfilPicture();
        }

        // Remove empty post's values
        foreach ($this->post as $key => $value) {
            if (empty($value)) {
                $this->post[$key] = null;
            }
        }

        // Checks if a new password is given
        if (!empty($this->post['password'])) {
            // Hash new password
            $this->post['password'] = password_hash($this->post['password'], PASSWORD_BCRYPT);
        } else {
            // Unset user password
            unset($this->post['password']);
        }

        // Update user object
        $userDTO->hydrate($this->post);

        // Check if an user doesnt already exists with this email
        if (!empty($userDAO->getUserByEmail($userDTO->getEmail())) && $userDAO->getUserByEmail($userDTO->getEmail())->getId() !== $userDTO->getId()) {
            // Set error - user email already exists
            $this->session['form-errors'] = ['email' => ['Un utilisateur avec cette adresse email existe déjà !']];
            // Set data inputs from post data
            $this->session['form-inputs'] = $this->post;
            // Redirect
            $this->redirect('/admin/utilisateur/'.$userId);
        }

        // Check if an user doesnt already exists with this pseudo
        if (!empty($userDAO->getUserByPseudo($userDTO->getPseudo())) && $userDAO->getUserByPseudo($userDTO->getPseudo())->getId() !== $userDTO->getId()) {
            // Set error - user with this pseudo exists
            $this->session['form-errors'] = ['pseudo' => ['Un utilisateur avec ce pseudo existe déjà !']];
            // Set form inputs data from post data
            $this->session['form-inputs'] = $this->post;
            // Redirect
            $this->redirect('/admin/utilisateur/'.$userId);
        }

        // Update user
        $user = $userDAO->save($userDTO);

        // Check if user has been updated
        if (!$user) {
            // Set error internal - user not updated
            $this->session['flash-error'] = "Erreur interne ! Aucune modification n'a pu être enregistrée.";
            // Set form inputs data from post data
            $this->session['form-inputs'] = $this->post;
            // Redirect
            $this->redirect('/admin/utilisateur/'.$userId);
        }

        // If there is a new picture
        if (!empty($newPictureTmpName)) {
            // Upload the picture
            move_uploaded_file($newPictureTmpName, __DIR__.'/../../assets/img/user/profil_picture/' . basename($userDTO->getProfilPicture()));
            // If there is an old picture
            if (!empty($oldPicture)) {
                // Remove old picture
                unlink(__DIR__.'/../../assets/img/user/profil_picture/' . $oldPicture);
            }
        }

        // Set success message - User updated
        $this->session['flash-success'] = "Modifications enregistrées.";
        // Redirect
        $this->redirect('/admin/utilisateurs');
    }

    // Show admin lists comments
    public function listComments()
    {
        // Get all comments submitted
        $comments = new CommentDAO();
        $filters = ['status' => 'NULL'];
        $comments = $comments->getAll($filters);

        // Show admin comments lists view
        $this->render('admin/comments.html.twig', ['comments' => $comments]);
    }

    // Show admin edit aboutMe
    public function aboutMe()
    {
        // get AboutMe
        $aboutMe = new AboutMeDAO();
        $aboutMe = $aboutMe->getAboutMe();

        // Show admin edit aboutMe view
        $this->render('admin/edit_aboutme.html.twig', ['aboutMe' => $aboutMe]);
    }

    // Update AboutMe
    public function editAboutMe()
    {
        // Check if missing required data
        if (empty($this->post)) {
            // Set error - missing required data
            $this->session['flash-error'] = "Aucune donnée reçu !";
            // Redirect
            $this->redirect('/admin/a-propos');
        }

        // If there is file uploaded add filenames in post data
        if(!empty($_FILES)) {
            foreach ($_FILES as $inputName => $file) {
                $this->post[$inputName] = $file;
            }
        }

        // Add form validator rules
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

        // Check if is valid form
        if (!empty($form->validate($rules, $this->post))) {
            $this->session['form-errors'] = $form->getErrors();
            $this->session['form-inputs'] = $this->post;
            $this->redirect('/admin/a-propos');
        }

        // Get aboutMe
        $aboutMeDAO = new AboutMeDAO();
        $aboutMeDTO = $aboutMeDAO->getAboutMe();

        // Check if found aboutMe
        if(empty($aboutMeDTO)) {
            // Set error - aboutMe not found
            $this->session['flash-error'] = 'Erreur Interne, aboutMe doesnt find';
            // Redirect
            $this->redirect('/admin/tableau-de-bord');
        }

        // If picture uploaded
        if (!empty($this->post['profil_picture']['tmp_name'])) {
            // Set picture data
            $newProfilPictureTmpName = $this->post['profil_picture']['tmp_name'];
            // Get old Picture name
            $oldProfilPicture = $aboutMeDTO->getProfilPicture();
            // Set post picture name
            $this->post['profil_picture'] = uniqid().'.png';
        } else {
            // Set post picture name with the old one
            $this->post['profil_picture'] = $aboutMeDTO->getProfilPicture();
        }

        // If picture uploaded
        if (!empty($this->post['cv_pdf']['tmp_name'])) {
            // Set cv_pdf data
            $newCvPdfTmpName = $this->post['cv_pdf']['tmp_name'];
            // Get old cv_pdf name
            $oldCvPdf = $aboutMeDTO->getCvPdf();
            // Set post cv_pdf name
            $this->post['cv_pdf'] = uniqid().'.pdf';
        } else {
            // Set post cv_pdf name with the old one
            $this->post['cv_pdf'] = $aboutMeDTO->getCvPdf();
        }

        // If picture uploaded
        if (!empty($this->post['picture']['tmp_name'])) {
            // Set picture data
            $newPictureTmpName = $this->post['picture']['tmp_name'];
            // Get old picture name
            $oldPicture = $aboutMeDTO->getPicture();
            // Set post picture name
            $this->post['picture'] = uniqid().'.png';
        } else {
            // Set post picture name with the old one
            $this->post['picture'] = $aboutMeDTO->getPicture();
        }

        // Remove empty post's values
        foreach ($this->post as $key => $value) {
            if (empty($value)) {
                $this->post[$key] = null;
            }
        }

        // Update AboutMe object
        $aboutMeDTO->hydrate($this->post);

        // Update AboutMe
        $aboutMe = $aboutMeDAO->save($aboutMeDTO);

        // Check if aboutMe has been updated
        if (!$aboutMe) {
            // Set error - Internal error - AboutMe not updated
            $this->session['flash-error'] = "Erreur interne ! Aucune modification n'a pu être enregistrée.";
            // Redirect
            $this->redirect('/admin/a-propos');
        }

        // If there is a new profil_picture
        if (!empty($newProfilPictureTmpName)) {
            // Upload the picture
            move_uploaded_file($newProfilPictureTmpName, __DIR__.'/../../assets/aboutme/' . basename($aboutMeDTO->getProfilPicture()));
            // If there is an old profil_picture
            if (!empty($oldProfilPicture)) {
                // Remove old profil_picture
                unlink(__DIR__.'/../../assets/aboutme/' . $oldProfilPicture);
            }
        }

        // If there is a new cv_pdf
        if (!empty($newCvPdfTmpName)) {
            // Upload the picture
            move_uploaded_file($newCvPdfTmpName, __DIR__.'/../../assets/aboutme/' . basename($aboutMeDTO->getCvPdf()));
            // If there is an old cv_pdf
            if (!empty($oldCvPdf)) {
                // Remove old cv_pdf
                unlink(__DIR__.'/../../assets/aboutme/' . $oldCvPdf);
            }
        }

        // If there is a new picture
        if (!empty($newPictureTmpName)) {
            // Upload the picture
            move_uploaded_file($newPictureTmpName, __DIR__.'/../../assets/aboutme/' . basename($aboutMeDTO->getPicture()));
            // If there is an old picture
            if (!empty($oldPicture)) {
                // Remove old picture
                unlink(__DIR__.'/../../assets/aboutme/' . $oldPicture);
            }
        }

        // Set success message - AboutMe updated
        $this->session['flash-success'] = "Modifications enregistrées.";
        // Redirect
        $this->redirect('/admin/a-propos');
    }
}