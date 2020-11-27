<?php


namespace App\Controllers;


use App\DAO\CommentDAO;
use App\DAO\PostDAO;
use App\DAO\UserDAO;

class ApiController extends Controller
{
    public function __construct() {
        parent::__construct();
        header('Content-Type: application/json');
    }

    public function validateComment()
    {
        if (empty($_SESSION) || $_SESSION['role'] !== 'ROLE_ADMIN') {
            http_response_code(500);
            $this->session['flash-error'] = "Vous ne pouvez pas accéder à cette partie du site.";
            die(json_encode(['success' => false, 'msg' => 'Internal Error']));
        }

        if (empty($_POST) || empty($_POST['id'])) {
            http_response_code(500);
            $this->session['flash-error'] = "Des données requises sont manquantes.";
            die(json_encode(['success' => false, 'msg' => 'Missing required data']));
        }

        $commentDAO = new CommentDAO();
        if ($commentDAO->editCommentStatus($_POST['id'], 'validated')) {
            http_response_code(200);
            $this->session['flash-success'] = "Commentaire validé !";
            die(json_encode(['success' => true, 'msg' => 'Comment validated successfuly']));
        } else {
            http_response_code(500);
            $this->session['flash-error'] = "Erreur Interne ! Le commentaire n'a pas pu être validé.";
            die(json_encode(['success' => false, 'msg' => 'Internal Error']));
        }
    }

    public function unvalidateComment()
    {
        if (empty($_SESSION) || $_SESSION['role'] !== 'ROLE_ADMIN') {
            http_response_code(500);
            $this->session['flash-error'] = "Vous ne pouvez pas accéder à cette partie du site.";
            die(json_encode(['success' => false, 'msg' => 'Internal Error']));
        }

        if (empty($_POST) || empty($_POST['id'])) {
            http_response_code(500);
            $this->session['flash-error'] = "Des données requises sont manquantes.";
            die(json_encode(['success' => false, 'msg' => 'Missing required data']));
        }

        $commentDAO = new CommentDAO();
        if ($commentDAO->editCommentStatus($_POST['id'], 'unvalidated')) {
            http_response_code(200);
            $this->session['flash-success'] = "Commentaire invalidé.";
            //@TODO Send an email to the user
            die(json_encode(['success' => true, 'msg' => 'Comment unvalidated successfuly']));
        } else {
            http_response_code(500);
            $this->session['flash-error'] = "Erreur Interne ! Le commentaire n'a pas pu être invalidé.";
            die(json_encode(['success' => false, 'msg' => 'Internal Error']));
        }
    }

    public function archivePost()
    {
        if (empty($_SESSION) || $_SESSION['role'] !== 'ROLE_ADMIN') {
            http_response_code(500);
            $this->session['flash-error'] = "Vous ne pouvez pas accéder à cette partie du site.";
            die(json_encode(['success' => false, 'msg' => 'Internal Error']));
        }

        if (empty($this->post['id'])) {
            http_response_code(500);
            $this->session['flash-error'] = "Des données requises sont manquantes.";
            die(json_encode(['success' => false, 'msg' => 'Missing required data']));
        }

        $postDAO = new PostDAO();
        $postDTO = $postDAO->getPostById($this->post['id']);

        if (empty($postDTO)) {
            http_response_code(404);
            $this->session['flash-error'] = "Erreur interne. Article non trouvé.";
            die(json_encode(['success' => false, 'msg' => 'Post didn\'t find']));
        }

        $postDTO->setIsArchived(true);
        $postDTO->setArchivedAt(date('Y-m-d H:i:s'));
        $postDTO = $postDAO->save($postDTO);

        if ($postDTO !== true) {
            http_response_code(500);
            $this->session['flash-error'] = "Erreur Interne ! L'article n'a pas pu être archivé.";
            die(json_encode(['success' => false, 'msg' => 'Internal Error']));
        }

        http_response_code(200);
        $this->session['flash-success'] = "Article archivé.";
        die(json_encode(['success' => true, 'msg' => 'Post Archived successfuly']));
    }

    public function unarchivePost()
    {
        if (empty($_SESSION) || $_SESSION['role'] !== 'ROLE_ADMIN') {
            http_response_code(500);
            $this->session['flash-error'] = "Vous ne pouvez pas accéder à cette partie du site.";
            die(json_encode(['success' => false, 'msg' => 'Internal Error']));
        }

        if (empty($this->post['id'])) {
            http_response_code(500);
            $this->session['flash-error'] = "Des données requises sont manquantes.";
            die(json_encode(['success' => false, 'msg' => 'Missing required data']));
        }

        $postDAO = new PostDAO();
        $postDTO = $postDAO->getPostById($this->post['id']);

        if (empty($postDTO)) {
            http_response_code(404);
            $this->session['flash-error'] = "Erreur interne. Article non trouvé.";
            die(json_encode(['success' => false, 'msg' => 'Post didn\'t find']));
        }

        $postDTO->setIsArchived(false);
        $postDTO->setArchivedAt(null);
        $postDTO = $postDAO->save($postDTO);

        if ($postDTO !== true) {
            http_response_code(500);
            $this->session['flash-error'] = "Erreur Interne ! L'article n'a pas pu être désarchivé.";
            die(json_encode(['success' => false, 'msg' => 'Internal Error']));
        }

        http_response_code(200);
        $this->session['flash-success'] = "Article désarchivé.";
        die(json_encode(['success' => true, 'msg' => 'Post Archived successfuly']));
    }

    public function deletePost(int $postId)
    {
        if (empty($_SESSION) || $_SESSION['role'] !== 'ROLE_ADMIN') {
            http_response_code(500);
            $this->session['flash-error'] = "Vous ne pouvez pas accéder à cette partie du site.";
            die(json_encode(['success' => false, 'msg' => 'Internal Error']));
        }

        $postDAO = new PostDAO();
        $postDTO = $postDAO->getPostById($postId, true);

        if (empty($postDTO)) {
            http_response_code(404);
            $this->session['flash-error'] = "Erreur interne. Article non trouvé.";
            die(json_encode(['success' => false, 'msg' => 'Post not find']));
        }

        $deletePost = $postDAO->delete($postDTO);

        if (empty($deletePost)) {
            http_response_code(500);
            $this->session['flash-error'] = "Erreur Interne ! L'article n'a pas pu être supprimé.";
            die(json_encode(['success' => false, 'msg' => 'Internal Error']));
        }

        http_response_code(200);
        $this->session['flash-success'] = "Article supprimé.";
        die(json_encode(['success' => true, 'msg' => 'Post Deleted successfuly']));
    }

    public function deactivateUser(int $idUser)
    {
        if (empty($_SESSION) || $_SESSION['role'] !== 'ROLE_ADMIN') {
            http_response_code(500);
            $this->session['flash-error'] = "Vous ne pouvez pas accéder à cette partie du site.";
            die(json_encode(['success' => false, 'msg' => 'Internal Error']));
        }

        $userDAO = new UserDAO();
        $userDTO = $userDAO->getUserById($idUser);

        if (empty($userDTO)) {
            http_response_code(404);
            $this->session['flash-error'] = "Erreur interne. Utilisateur non trouvé.";
            die(json_encode(['success' => false, 'msg' => 'User not find']));
        }

        $userDTO->setIsDeactivated(1);
        $userDTO->setDeactivatedAt(date('Y-m-d H:i:s'));
        $userDTO = $userDAO->save($userDTO);

        if ($userDTO !== true) {
            http_response_code(500);
            $this->session['flash-error'] = "Erreur Interne ! L'utilisateur n'a pas pu être désactivé.";
            die(json_encode(['success' => false, 'msg' => 'Internal Error']));
        }

        http_response_code(200);
        $this->session['flash-success'] = "Utilisateur désactivé.";
        die(json_encode(['success' => true, 'msg' => 'User Deactivated successfuly']));
    }

    public function reactivateUser(int $idUser)
    {
        if (empty($_SESSION) || $_SESSION['role'] !== 'ROLE_ADMIN') {
            http_response_code(500);
            $this->session['flash-error'] = "Vous ne pouvez pas accéder à cette partie du site.";
            die(json_encode(['success' => false, 'msg' => 'Internal Error']));
        }

        $userDAO = new UserDAO();
        $userDTO = $userDAO->getUserById($idUser);

        if (empty($userDTO)) {
            http_response_code(404);
            $this->session['flash-error'] = "Erreur interne. Utilisateur non trouvé.";
            die(json_encode(['success' => false, 'msg' => 'User not find']));
        }

        $userDTO->setIsDeactivated(0);
        $userDTO->setDeactivatedAt(null);
        $userDTO = $userDAO->save($userDTO);

        if ($userDTO !== true) {
            http_response_code(500);
            $this->session['flash-error'] = "Erreur Interne ! L'utilisateur n'a pas pu être réactivé.";
            die(json_encode(['success' => false, 'msg' => 'Internal Error']));
        }

        http_response_code(200);
        $this->session['flash-success'] = "Utilisateur réactivé.";
        die(json_encode(['success' => true, 'msg' => 'User Reactivated successfuly']));
    }
}