<?php


namespace App\Controllers;


class AdminController extends Controller {

    public function index() {
        if (empty($_SESSION)|| $_SESSION['role'] !== 'ROLE_ADMIN') {
            header('Location : /');
            //@TODO Display an error - User doesn't have the right access to admin
        }
    }
}