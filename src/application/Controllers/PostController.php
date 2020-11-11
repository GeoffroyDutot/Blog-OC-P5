<?php


namespace App\Controllers;


use App\DAO\AboutMeDAO;
use App\DAO\CommentDAO;
use App\DAO\PostDAO;
use App\DAO\UserDAO;
use App\DTO\CommentDTO;
use App\DTO\PostDTO;
use App\Form\FormValidator;

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
        //@TODO if is_archived and not admin redirect + error

        if (!empty($post)) {
            $data['post'] = $post;
        }

        $this->render('post.html.twig', $data);
    }

    public function submitComment(int $postId) {
        if (empty($this->session)) {
            //@TODO Display error
            echo 'Utilisateur non connecté';
            return;
        }

        if (empty($this->post)) {
            //@TODO Display error
            echo 'Aucune données';
            return;
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
            //@TODO Display error
            echo 'Formulaire non valide, vérifiez les informations';
            return;
        }

        $userDAO = new UserDAO();
        $user = $userDAO->getUserByPseudo($this->session['pseudo']);

        if (empty($user)) {
            //@TODO Display error
            echo 'Utilisateur non connecté';
            return;
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
            //@TODO Display error
            echo 'Erreur interne';
            return;
        }

        $this->redirect($_SERVER['HTTP_REFERER']);
    }
}