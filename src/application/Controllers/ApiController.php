<?php


namespace App\Controllers;


use App\DAO\CommentDAO;
use App\DAO\PostDAO;
use App\DAO\UserDAO;

class ApiController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // Set header in json format
        header('Content-Type: application/json');

        // Check if user logged and user is an admin
        if (empty($this->session) || $this->session['role'] !== 'ROLE_ADMIN') {
            // Set HTTP Status to 500
            http_response_code(500);
            // Set error message - user doesnt have rights
            $this->session['flash-error'] = "Vous ne pouvez pas accéder à cette partie du site.";
            // Return error
            print_r(json_encode(['success' => false, 'msg' => 'Internal Error']));
            return;
        }
    }

    // Validate a comment
    public function validateComment(int $idComment)
    {
        // Get comment by it's id
        $commentDAO = new CommentDAO();
        $commentDTO = $commentDAO->getCommentById($idComment);

        // If comment doesn't find
        if (empty($commentDTO)) {
            // Set HTTP status to 404
            http_response_code(404);
            // Set error message - comment doesn't find
            $this->session['flash-error'] = "Erreur Interne ! Le commentaire n'a pas été trouvé.";
            // Return error
            print_r(json_encode(['success' => true, 'msg' => 'Comment didn\'t find']));
            return;
        }

        // Set comment status validated
        $commentDTO->setStatus('validated');

        // Update comment
        $comment = $commentDAO->save($commentDTO);

        // If comment doesn't updated - set error
        if (!$comment) {
            // Set HTTP status to 500
            http_response_code(500);
            // Set error message - comment doesn't updated
            $this->session['flash-error'] = "Erreur Interne ! Le commentaire n'a pas pu être validé.";
            // Return error
            print_r(json_encode(['success' => false, 'msg' => 'Internal Error']));
            return;
        }

        // Set HTTP status 200
        http_response_code(200);
        // Set success message - validated comment
        $this->session['flash-success'] = "Commentaire validé !";
        // Return success
        print_r(json_encode(['success' => true, 'msg' => 'Comment validated successfuly']));
        return;
    }

    // Unvalidate a comment
    public function unvalidateComment(int $idComment)
    {
        // Get comment by it's id
        $commentDAO = new CommentDAO();
        $commentDTO = $commentDAO->getCommentById($idComment);

        // If comment doesn't find
        if (empty($commentDTO)) {
            // Set HTTP status to 404
            http_response_code(404);
            // Set error message - comment doesn't find
            $this->session['flash-error'] = "Erreur Interne ! Le commentaire n'a pas été trouvé.";
            // Return error
            print_r(json_encode(['success' => false, 'msg' => 'Comment didn\'t find']));
            return;
        }

        // Set comment status validated
        $commentDTO->setStatus('unvalidated');

        // Update comment
        $comment = $commentDAO->save($commentDTO);

        // If comment doesn't updated - set error
        if (!$comment) {
            // Set HTTP status to 500
            http_response_code(500);
            // Set error message - comment doesn't updated
            $this->session['flash-error'] = "Erreur Interne ! Le commentaire n'a pas pu être invalidé.";
            // Return error
            print_r(json_encode(['success' => false, 'msg' => 'Internal Error']));
            return;
        }

        //@TODO Send an email to the user
        // Set HTTP status 200
        http_response_code(200);
        // Set success message - validated comment
        $this->session['flash-success'] = "Commentaire invalidé !";
        // Return success
        print_r(json_encode(['success' => true, 'msg' => 'Comment unvalidated successfuly']));
        return;
    }

    // Archive post
    public function archivePost(int $idPost)
    {
        // Get post by it's id
        $postDAO = new PostDAO();
        $postDTO = $postDAO->getPostById($idPost);

        // Id post doesn't find
        if (empty($postDTO)) {
            // Set HTTP status to 404
            http_response_code(404);
            // Set error message - post doesn't find
            $this->session['flash-error'] = "Erreur interne. Article non trouvé.";
            // Return error
            print_r(json_encode(['success' => false, 'msg' => 'Post didn\'t find']));
            return;
        }

        // Set post status archived
        $postDTO->setIsArchived(true);
        // Set date archived at
        $postDTO->setArchivedAt(date('Y-m-d H:i:s'));

        // Update post
        $postDTO = $postDAO->save($postDTO);

        // If post doesn't updated - set error
        if (!$postDTO) {
            // Set HTTP status 500
            http_response_code(500);
            // Set error message - post doesn't updated
            $this->session['flash-error'] = "Erreur Interne ! L'article n'a pas pu être archivé.";
            // Return error
            print_r(json_encode(['success' => false, 'msg' => 'Internal Error']));
            return;
        }

        // Set HTTP status 200
        http_response_code(200);
        // Set success message - archived post
        $this->session['flash-success'] = "Article archivé.";
        // Return success
        print_r(json_encode(['success' => true, 'msg' => 'Post Archived successfuly']));
        return;
    }

    // Unarchive post
    public function unarchivePost(int $idPost)
    {
        // Get post by it's id
        $postDAO = new PostDAO();
        $postDTO = $postDAO->getPostById($idPost);

        // Id post doesn't find
        if (empty($postDTO)) {
            // Set HTTP status to 404
            http_response_code(404);
            // Set error message - post doesn't find
            $this->session['flash-error'] = "Erreur interne. Article non trouvé.";
            // Return error
            print_r(json_encode(['success' => false, 'msg' => 'Post didn\'t find']));
            return;
        }

        // Set post status to 0
        $postDTO->setIsArchived(false);
        // Unset post archived at datetime
        $postDTO->setArchivedAt(null);

        // Update post
        $postDTO = $postDAO->save($postDTO);

        // If post doesn't updated - set error
        if (!$postDTO) {
            // Set HTTP status 500
            http_response_code(500);
            // Set error message - post doesn't updated
            $this->session['flash-error'] = "Erreur Interne ! L'article n'a pas pu être désarchivé.";
            // Return error
            print_r(json_encode(['success' => false, 'msg' => 'Internal Error']));
            return;
        }

        // Set HTTP status 200
        http_response_code(200);
        // Set success message - unarchived post
        $this->session['flash-success'] = "Article désarchivé.";
        // Return success
        print_r(json_encode(['success' => true, 'msg' => 'Post Archived successfuly']));
        return;
    }

    // Delete post
    public function deletePost(int $postId)
    {
        // Get post by id
        $postDAO = new PostDAO();
        $postDTO = $postDAO->getPostById($postId, true);

        // If post doesn't find
        if (empty($postDTO)) {
            // Set HTTP status 404
            http_response_code(404);
            // Set error post doesn't find
            $this->session['flash-error'] = "Erreur interne. Article non trouvé.";
            // Return error
            print_r(json_encode(['success' => false, 'msg' => 'Post not find']));
            return;
        }

        // Delete post
        $deletePost = $postDAO->delete($postDTO);

        // If post doesn't deleted
        if (empty($deletePost)) {
            // Set HTTP status 500
            http_response_code(500);
            // Set error message - post doesnt deleted
            $this->session['flash-error'] = "Erreur Interne ! L'article n'a pas pu être supprimé.";
            // Return error
            print_r(json_encode(['success' => false, 'msg' => 'Internal Error']));
            return;
        }

        if (!empty($postDTO->getPicture())) {
            // Remove post picture
            unlink(__DIR__.'/../../assets/img/post/' . $postDTO->getPicture());
        }

        // Set HTTP status 200
        http_response_code(200);
        // Set success message - post deleted
        $this->session['flash-success'] = "Article supprimé.";
        // Return success
        print_r(json_encode(['success' => true, 'msg' => 'Post Deleted successfuly']));
        return;
    }

    // Deactivate an user
    public function deactivateUser(int $idUser)
    {
        // Get an user by it's id
        $userDAO = new UserDAO();
        $userDTO = $userDAO->getUserById($idUser);

        // If user doesn't find
        if (empty($userDTO)) {
            // Set HTTP status 404
            http_response_code(404);
            // Set error user doesn't find
            $this->session['flash-error'] = "Erreur interne. Utilisateur non trouvé.";
            // Return error
            print_r(json_encode(['success' => false, 'msg' => 'User not find']));
            return;
        }

        // Set user status to deactivated
        $userDTO->setIsDeactivated(1);
        // Set datetime to deactivated_at
        $userDTO->setDeactivatedAt(date('Y-m-d H:i:s'));

        // Update user
        $userDTO = $userDAO->save($userDTO);

        // If user doesn't updated
        if (!$userDTO) {
            // Set HTTP status 500
            http_response_code(500);
            // Set error user doesn't deactivated
            $this->session['flash-error'] = "Erreur Interne ! L'utilisateur n'a pas pu être désactivé.";
            // Return error
            print_r(json_encode(['success' => false, 'msg' => 'Internal Error']));
            return;
        }

        //@TODO Send email
        // Set HTTP status to 200
        http_response_code(200);
        // Set success message - user deactivated
        $this->session['flash-success'] = "Utilisateur désactivé.";
        // Return success
        print_r(json_encode(['success' => true, 'msg' => 'User Deactivated successfuly']));
        return;
    }

    // Reactivate an user
    public function reactivateUser(int $idUser)
    {
        // Get an user by it's id
        $userDAO = new UserDAO();
        $userDTO = $userDAO->getUserById($idUser);

        // If user doesn't find
        if (empty($userDTO)) {
            // Set HTTP status 404
            http_response_code(404);
            // Set error user doesn't find
            $this->session['flash-error'] = "Erreur interne. Utilisateur non trouvé.";
            // Return error
            print_r(json_encode(['success' => false, 'msg' => 'User not find']));
            return;
        }

        // Set user status to activated
        $userDTO->setIsDeactivated(0);
        // Unset user datetime deactivated_at
        $userDTO->setDeactivatedAt(null);

        // Update user
        $userDTO = $userDAO->save($userDTO);

        // If user doesn't updated
        if (!$userDTO) {
            // Set HTTP status 500
            http_response_code(500);
            // Set error user doesn't reactivated
            $this->session['flash-error'] = "Erreur Interne ! L'utilisateur n'a pas pu être réactivé.";
            // Return error
            print_r(json_encode(['success' => false, 'msg' => 'Internal Error']));
            return;
        }

        //@TODO Send email
        // Set HTTP status to 200
        http_response_code(200);
        // Set success message - user reactivated
        $this->session['flash-success'] = "Utilisateur réactivé.";
        // Return success
        print_r(json_encode(['success' => true, 'msg' => 'User Reactivated successfuly']));
        return;
    }
}