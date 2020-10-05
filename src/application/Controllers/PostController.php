<?php


namespace App\Controllers;


use App\DAO\AboutMeDAO;
use App\DAO\CommentDAO;
use App\DAO\PostDAO;
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
}