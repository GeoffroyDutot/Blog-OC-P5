<?php


namespace App\Controllers;


use App\DAO\CommentDAO;
use App\DAO\PostDAO;
use App\DAO\UserDAO;
use App\DTO\PostDTO;

class ApiController extends Controller {

    public function __construct() {
        parent::__construct();
        header('Content-Type: application/json');
    }

    public function validateComment() {
        if (empty($_SESSION) || $_SESSION['role'] !== 'ROLE_ADMIN') {
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
        if (empty($_SESSION) || $_SESSION['role'] !== 'ROLE_ADMIN') {
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

    public function archivePost() {
        if (empty($_SESSION) || $_SESSION['role'] !== 'ROLE_ADMIN') {
            http_response_code(500);
            die(json_encode(['success' => false, 'msg' => 'Internal Error']));
            //@TODO Display an error - User doesn't have the right access to admin
        }

        if (empty($this->post['id'])) {
            http_response_code(500);
            die(json_encode(['success' => false, 'msg' => 'Missing required data']));
            //@TODO Display an error - Missing required data
        }

        $postDAO = new PostDAO();
        $postDTO = $postDAO->getPostById($this->post['id']);

        if (empty($postDTO)) {
            http_response_code(404);
            die(json_encode(['success' => false, 'msg' => 'Post didn\'t find']));
            //@TODO Display an error - Post didn't find
        }

        $postDTO->setIsArchived(true);
        $postDTO->setArchivedAt(date('Y-m-d H:i:s'));
        $postDTO = $postDAO->save($postDTO);

        if ($postDTO !== true) {
            http_response_code(500);
            die(json_encode(['success' => false, 'msg' => 'Internal Error']));
            //@TODO Display an error - Internal Error
        }

        http_response_code(200);
        die(json_encode(['success' => true, 'msg' => 'Post Archived successfuly']));
        //@TODO Display a success msg
    }

    public function unarchivePost() {
        if (empty($_SESSION) || $_SESSION['role'] !== 'ROLE_ADMIN') {
            http_response_code(500);
            die(json_encode(['success' => false, 'msg' => 'Internal Error']));
            //@TODO Display an error - User doesn't have the right access to admin
        }

        if (empty($this->post['id'])) {
            http_response_code(500);
            die(json_encode(['success' => false, 'msg' => 'Missing required data']));
            //@TODO Display an error - Missing required data
        }

        $postDAO = new PostDAO();
        $postDTO = $postDAO->getPostById($this->post['id']);

        if (empty($postDTO)) {
            http_response_code(404);
            die(json_encode(['success' => false, 'msg' => 'Post didn\'t find']));
            //@TODO Display an error - Post didn't find
        }

        $postDTO->setIsArchived(false);
        $postDTO->setArchivedAt(null);
        $postDTO = $postDAO->save($postDTO);

        if ($postDTO !== true) {
            http_response_code(500);
            die(json_encode(['success' => false, 'msg' => 'Internal Error']));
            //@TODO Display an error - Internal Error
        }

        http_response_code(200);
        die(json_encode(['success' => true, 'msg' => 'Post Archived successfuly']));
        //@TODO Display a success msg
    }

    public function deletePost(int $postId) {
        if (empty($_SESSION) || $_SESSION['role'] !== 'ROLE_ADMIN') {
            http_response_code(500);
            die(json_encode(['success' => false, 'msg' => 'Internal Error']));
            //@TODO Display an error - User doesn't have the right access to admin
        }

        $postDAO = new PostDAO();
        $postDTO = $postDAO->getPostById($postId, true);

        if (empty($postDTO)) {
            http_response_code(404);
            die(json_encode(['success' => false, 'msg' => 'Post not find']));
            //@TODO Display an error - Post not found
        }

        $deletePost = $postDAO->delete($postDTO);

        if (empty($deletePost)) {
            http_response_code(500);
            die(json_encode(['success' => false, 'msg' => 'Internal Error']));
            //@TODO Display an error - Internal Error
        }

        http_response_code(200);
        die(json_encode(['success' => true, 'msg' => 'Post Deleted successfuly']));
        //@TODO Display a success msg
    }

    public function deactivateUser(int $idUser) {
        if (empty($_SESSION) || $_SESSION['role'] !== 'ROLE_ADMIN') {
            http_response_code(500);
            die(json_encode(['success' => false, 'msg' => 'Internal Error']));
            //@TODO Display an error - User doesn't have the right access to admin
        }

        $userDAO = new UserDAO();
        $userDTO = $userDAO->getUserById($idUser);

        if (empty($userDTO)) {
            http_response_code(404);
            die(json_encode(['success' => false, 'msg' => 'User not find']));
            //@TODO Display an error - Post not found
        }

        $userDTO->setIsDeactivated(1);
        $userDTO->setDeactivatedAt(date('Y-m-d H:i:s'));
        $userDTO = $userDAO->save($userDTO);

        if ($userDTO !== true) {
            http_response_code(500);
            die(json_encode(['success' => false, 'msg' => 'Internal Error']));
            //@TODO Display an error - Internal Error
        }

        http_response_code(200);
        die(json_encode(['success' => true, 'msg' => 'User Deactivated successfuly']));
        //@TODO Display a success
    }
}