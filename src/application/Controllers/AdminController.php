<?php


namespace App\Controllers;


use App\DAO\PostDAO;
use App\DAO\UserDAO;

class AdminController extends Controller {

    public function index() {
        if (empty($_SESSION)|| $_SESSION['role'] !== 'ROLE_ADMIN') {
            $this->redirect('/');
            //@TODO Display an error - User doesn't have the right access to admin
        }

        $posts = new PostDAO();
        $posts = $posts->getAll(5);

        $users = new UserDAO();
        $users = $users->getAll(5);

        $data['posts'] = $posts;
        $data['users'] = $users;

        $this->render('admin/dashboard.html.twig', $data);
    }

    public function listUsers() {

    }

    public function listPosts() {

    }
}