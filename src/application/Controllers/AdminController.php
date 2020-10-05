<?php


namespace App\Controllers;


use App\DAO\CommentDAO;
use App\DAO\PostDAO;
use App\DAO\UserDAO;

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
}