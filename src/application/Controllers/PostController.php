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
    public function index()
    {
       $data = [];

        $aboutMe = new AboutMeDAO();
        $aboutMe = $aboutMe->getAboutMe();
        $data['aboutMe'] = $aboutMe;

       $posts = new PostDAO();
       $posts = $posts->getAll();

       if (!empty($posts)) {
           $data['posts'] = $posts;
       }

        $this->render('posts.html.twig', $data);
    }

    public function show(string $slug)
    {
        $data = [];

        $aboutMe = new AboutMeDAO();
        $aboutMe = $aboutMe->getAboutMe();
        $data['aboutMe'] = $aboutMe;

        $post = new PostDAO();
        $postDTO = $post->getPostBySlug($slug);

        if (empty($postDTO)) {
            $this->redirect('/page-introuvable');
        }

        if (!empty($postDTO) && $postDTO->getIsArchived() && $this->session['role'] !== 'ROLE_ADMIN') {
            $this->redirect('/page-introuvable');
        }

        if (!empty($postDTO)) {
            $data['post'] = $postDTO;
        }

        $this->render('post.html.twig', $data);
    }

    public function submitComment(int $postId)
    {
        if (empty($this->session)) {
            $this->session['flash-error'] = "Utilisateur non connecté !";
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }

        if (empty($this->post)) {
            $this->session['flash-error'] = "Aucune données reçues !";
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }

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

        if (!empty($form->validate($rules, $this->post))) {
            $this->session['form-errors'] = $form->getErrors();
            $this->session['form-inputs'] = $this->post;
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }

        $userDAO = new UserDAO();
        $user = $userDAO->getUserByPseudo($this->session['pseudo']);

        if (empty($user)) {
            $this->session['flash-error'] = "Utilisateur non reconnu";
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }

        $commentDTO = new CommentDTO();
        $commentDTO->setContent($this->post['comment']);
        $commentDTO->setIdPost($postId);
        $commentDTO->setIdUser($user->getId());

        if ($user->getRole() === 'ROLE_ADMIN') {
            $commentDTO->setStatus('validated');
        } else {
            $commentDTO->setStatus(null);
        }

        $commentDAO = new CommentDAO();
        $comment = $commentDAO->submitComment($commentDTO);
        if (empty($comment)) {
            $this->session['flash-error'] = "Erreur interne, le commentaire n'a pas pu être envoyé.";
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }

        $this->session['flash-success'] = "Commentaire correctement soumis.";
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
    }
}