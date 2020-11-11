<?php


namespace App\DAO;


use App\DTO\CommentDTO;

class CommentDAO extends DAO {

    public function getAllCommentsUnvalidated($limit = null) : array {
        $db = $this->connectDb();
        $comments = [];

        $query = 'SELECT * FROM `comment` WHERE `status` IS NULL ORDER BY `created_at` DESC ';

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

    public function getByPostId(array $filters) : array {
        $db = $this->connectDb();
        $comments = [];

        $query = "SELECT * FROM `comment`";

        if (empty($filters['postId'])) {
            return null;
        }

        $query .= " WHERE `id_post` = ".$filters['postId'];

        if (!empty($filters['status'])) {
            $query .= " AND `status` = " .'"'.$filters['status'].'"';
        }

        $req = $db->query($query);

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

    public function submitComment(CommentDTO $commentDTO) {
        $db = $this->connectDb();

        $req = $db->prepare('INSERT INTO `comment`(`content`, `id_post`, `id_user`, `status`, `created_at`) VALUES(:content, :id_post, :id_user, :status, :createdAt)');
        return $req->execute(['content' => $commentDTO->getContent(), 'id_post' => $commentDTO->getIdPost(), 'id_user' => $commentDTO->getIdUser(), 'status' => $commentDTO->getStatus(), 'createdAt' => date('Y-m-d H:i:s')]);
    }

    public function editCommentStatus(int $id, string $status) {
        $db = $this->connectDb();

        $req = $db->prepare('UPDATE comment SET status=:status WHERE id = \''.$id.'\'');
        return $req->execute(['status' => $status]);
    }

    public function delete(CommentDTO $commentDTO)
    {
        $db = $this->connectDb();

        if (empty($commentDTO->getId())) {
            return null;
        }

        return $db->exec('DELETE FROM comment WHERE id = '.$commentDTO->getId());
    }
}