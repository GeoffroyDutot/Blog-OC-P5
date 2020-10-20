<?php


namespace App\Controllers;


use App\DAO\CommentDAO;

class ApiController extends Controller {

    public function __construct() {
        parent::__construct();
        header('Content-Type: application/json');
    }

    public function validateComment() {
        if (empty($_SESSION)|| $_SESSION['role'] !== 'ROLE_ADMIN') {
            http_response_code(500);
            die(json_encode(['success' => false, 'msg' => 'Internal Error']));
            //@TODO Display an error - User doesn't have the right access to admin
        }

        if (empty($_POST) || empty($_POST['id'])) {
            http_response_code(500);
            die(json_encode(['success' => false, 'msg' => 'Missing required data']));
            //@TODO Display an error - Missing required data
        }

        $commentDAO = new CommentDAO();
        if ($commentDAO->editCommentStatus($_POST['id'], 'validated')) {
            http_response_code(200);
            //@TODO Display a success message
            die(json_encode(['success' => true, 'msg' => 'Comment validated successfuly']));
        } else {
            http_response_code(500);
            die(json_encode(['success' => false, 'msg' => 'Internal Error']));
            //@TODO Display an error - Internal Error
        }
    }

    public function unvalidateComment() {
        if (empty($_SESSION)|| $_SESSION['role'] !== 'ROLE_ADMIN') {
            http_response_code(500);
            die(json_encode(['success' => false, 'msg' => 'Internal Error']));
            //@TODO Display an error - User doesn't have the right access to admin
        }

        if (empty($_POST) || empty($_POST['id'])) {
            http_response_code(500);
            die(json_encode(['success' => false, 'msg' => 'Missing required data']));
            //@TODO Display an error - Missing required data
        }

        $commentDAO = new CommentDAO();
        if ($commentDAO->editCommentStatus($_POST['id'], 'unvalidated')) {
            http_response_code(200);
            //@TODO Display a success message
            //@TODO Send an email to the user
            die(json_encode(['success' => true, 'msg' => 'Comment unvalidated successfuly']));
        } else {
            http_response_code(500);
            die(json_encode(['success' => false, 'msg' => 'Internal Error']));
            //@TODO Display an error - Internal Error
        }
    }
}