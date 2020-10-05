<?php


namespace App\Controllers;


use App\DAO\AboutMeDAO;
use App\DAO\CommentDAO;
use App\DAO\PostDAO;
use App\DAO\UserDAO;
use App\DTO\CommentDTO;
use App\DTO\PostDTO;

class PostController extends Controller {
    public function index() {
       $data = [];

        $aboutMe = new AboutMeDAO();
        $aboutMe = $aboutMe->getAboutMe();
        $data['aboutMe'] = $aboutMe;

       $posts = new PostDAO();
       $posts = $posts->getAll();

       if ($posts) {
           $data['posts'] = $posts;
       }

        $this->render('posts.html.twig', $data);
    }

    public function show(string $slug) {
        $data = [];

        $aboutMe = new AboutMeDAO();
        $aboutMe = $aboutMe->getAboutMe();
        $data['aboutMe'] = $aboutMe;

        $post = new PostDAO();
        $post = $post->getPostBySlug($slug);

        if (!empty($post)) {
            $data['post'] = $post;
            $comments = new CommentDAO();
            $comments = $comments->getCommentsByPost($post->getId());
            if (!empty($comments)) {
                $data['comments'] = $comments;
            }
        }

        $this->render('post.html.twig', $data);
    }

    public function submitComment(int $id) {
        if (empty($_POST)) {
            echo 'Aucune données';
            return;
        }

        if (empty($this->session)) {
            echo 'Utilisateur non connecté';
            return;
        }

        $userDAO = new UserDAO();
        $user = $userDAO->getUserByPseudo($this->session['pseudo']);

        if (empty($user)) {
            echo 'Utilisateur non connecté';
            return;
        }
        $commentDTO = new CommentDTO();
        $commentDTO->setContent($_POST['comment']);
        $commentDTO->setIdPost($id);
        $commentDTO->setIdUser($user->getId());

        if ($user->getRole() === 'ROLE_ADMIN') {
            $commentDTO->setStatus('validated');
        } else {
            $commentDTO->setStatus('unvalidated');
        }

        $commentDAO = new CommentDAO();
        $comment = $commentDAO->submitComment($commentDTO);
        if (!empty($comment)) {
            $this->redirect($_SERVER['HTTP_REFERER']);
        }
        echo 'Erreur interne';
    }
}