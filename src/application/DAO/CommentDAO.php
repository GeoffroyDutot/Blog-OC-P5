<?php


namespace App\DAO;


use App\DTO\CommentDTO;

class CommentDAO extends DAO {

    public function getAllCommentsUnvalidated($limit = null) : array {
        $db = $this->connectDb();
        $comments = [];

        $query = 'SELECT * FROM `comment` WHERE `status` = "unvalidated" ORDER BY `created_at` DESC ';

        if ($limit) {
            $limit = 'LIMIT ' . $limit;
        }

        $req = $db->query($query . $limit);

        $data = $req->fetchAll(\PDO::FETCH_ASSOC);

        if (!empty($data)) {
            foreach ($data as $comment) {
                $comments[] = new CommentDTO($comment);
            }

            foreach ($comments as $comment) {
                //Retrieves User of comment
                $userDAO = new UserDAO();
                $user = $userDAO->getUserById($comment->getIdUser());
                if (!empty($user)) {
                    $comment->setUserDTO($user);
                }
            }
        }

        return  $comments;
    }

    public function getCommentsByPost(int $id_post) : array {
        $db = $this->connectDb();
        $comments = [];

        $req = $db->query('SELECT * FROM `comment` WHERE (`id_post` = '.$id_post.' AND `status` = "validated")');

        $data = $req->fetchAll(\PDO::FETCH_ASSOC);

        if (!empty($data)) {
            foreach ($data as $comment) {
                $comments[] = new CommentDTO($comment);
            }

            foreach ($comments as $comment) {
                //Retrieves User of comment
                $userDAO = new UserDAO();
                $user = $userDAO->getUserById($comment->getIdUser());
                if (!empty($user)) {
                    $comment->setUserDTO($user);
                }
            }
        }

        return  $comments;
    }
}