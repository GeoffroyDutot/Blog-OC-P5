<?php


namespace App\Controllers;


use App\DAO\AboutMeDAO;
use App\DAO\CommentDAO;
use App\DAO\PostDAO;
use App\DAO\UserDAO;
use App\DTO\CommentDTO;
use App\Form\FormValidator;

class PostController extends Controller
{
    // Posts list view
    public function index(int $page)
    {
        $data = [];

        // Get AboutMe infos
        $aboutMe = new AboutMeDAO();
        $aboutMe = $aboutMe->getAboutMe();
        $data['aboutMe'] = $aboutMe;

        // Get all posts
        $postDAO = new PostDAO();

        // Count total of posts
        $totalPosts = $postDAO->count()['nb'];

        // Set limit to 5 post per page
        $limitPerPage = 5;

        // Calculate total pages
        $totalPages = ceil($totalPosts / $limitPerPage);

        $page = !empty($page) ? $page : 1;

        // Offset
        $offset = ($page - 1) * $limitPerPage;

        // Add filter on post's status
        $filtersPosts = ['is_archived' => 0];
        $posts = $postDAO->getAll($filtersPosts, $limitPerPage, $offset);

        // Prev + Next
        $prev = $page - 1;
        $next = $page + 1;

        // Show posts list view
        $this->render('posts.html.twig', ['aboutMe' => $aboutMe, 'posts' => $posts, 'page' => $page, 'prev' => $prev, 'next' => $next, 'totalPages' => $totalPages]);
    }

    // Show post detail
    public function show(string $slug)
    {
        $data = [];

        // Get AboutMe infos
        $aboutMe = new AboutMeDAO();
        $aboutMe = $aboutMe->getAboutMe();
        $data['aboutMe'] = $aboutMe;

        // Get post by it's slug
        $post = new PostDAO();
        $postDTO = $post->getPostBySlug($slug);

        // If post doesn't find redirect 404
        if (empty($postDTO)) {
            // Redirect to 404
            $this->redirect('/page-introuvable');
        }

        // If post is archived and user not admin redirect 404
        if ($postDTO->getIsArchived() && $this->session['role'] !== 'ROLE_ADMIN') {
            // Redirect to 404
            $this->redirect('/page-introuvable');
        }

        // Set data post
        $data['post'] = $postDTO;

        // Show detail post view
        $this->render('post.html.twig', $data);
    }

    // Submit a comment
    public function submitComment(int $postId)
    {
        // Checks if user is connected
        if (empty($this->session)) {
            // Set error user not connected
            $this->session['flash-error'] = "Utilisateur non connecté !";
            // Redirect
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }

        // Checks if data is submitted
        if (empty($this->post)) {
            // Set error empty data
            $this->session['flash-error'] = "Aucune données reçues !";
            // Redirect
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }

        // Set form validator rules
        $form = new FormValidator();
        $rules = [
            [
                'fieldName' => 'comment',
                'type' => 'string',
                'minLength' => 5,
                'maxLength' => 255,
                'required' => true,
            ]
        ];

        // Checks if is valid form
        if (!empty($form->validate($rules, $this->post))) {
            // Set errors
            $this->session['form-errors'] = $form->getErrors();
            // Set data inputs
            $this->session['form-inputs'] = $this->post;
            // Redirect
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }

        // Get user by pseudo
        $userDAO = new UserDAO();
        $user = $userDAO->getUserByPseudo($this->session['pseudo']);

        // If user doesn't find set error
        if (empty($user)) {
            /// Set error user doesn't find
            $this->session['flash-error'] = "Utilisateur non reconnu";
            // Redirect
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }

        // Set comment object
        $commentDTO = new CommentDTO();
        $commentDTO->setContent($this->post['comment']);
        $commentDTO->setIdPost($postId);
        $commentDTO->setIdUser($user->getId());


        if ($user->getRole() === 'ROLE_ADMIN') {
            $commentDTO->setStatus('validated');
        } else {
            $commentDTO->setStatus(null);
        }

        // Create comment
        $commentDAO = new CommentDAO();
        $comment = $commentDAO->save($commentDTO);

        // If comment doesnt created error + redirect
        if (!($comment)) {
            // Set error message
            $this->session['flash-error'] = "Erreur interne, le commentaire n'a pas pu être envoyé.";
            // Redirect
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }

        // Set success message
        $this->session['flash-success'] = "Commentaire correctement soumis.";
        // Redirect
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
    }
}