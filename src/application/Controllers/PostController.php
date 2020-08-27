<?php


namespace App\Controllers;


use App\DAO\AboutMeDAO;
use App\DAO\PostDAO;
use App\DTO\PostDTO;

class PostController extends Controller{
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
}