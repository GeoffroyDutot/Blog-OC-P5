<?php


namespace App\DAO;


use App\DTO\CommentDTO;

class CommentDAO extends DAO
{
    // Get All the comments
    public function getAll(array $filters, $limit = null): array
    {
        $comments = [];

        // Retrieves comments where post's not archived and user's not deactivated
        $query = 'SELECT comment.* FROM `comment` JOIN user ON id_user = user.id JOIN post ON id_post = post.id WHERE user.is_deactivated = 0 AND post.is_archived = 0';

        // Add filter on comment's status
        if (!empty($filters['status'])) {
            $query .= ' AND status ' . ($filters['status'] === 'NULL' ? 'IS NULL' : '= "' . $filters['status']. '"');
        }

        // Order comment's on last date created
        $query .= ' ORDER BY comment.created_at DESC';

        // Add limit of comment's to return
        if (!empty($limit)) {
            $query .= ' LIMIT ' . $limit;
        }

        // Get's data on db
        $req = $this->db->query($query);
        $data = $req->fetchAll(\PDO::FETCH_ASSOC);

        if (!empty($data)) {
            // Creates Comments Objects
            foreach ($data as $comment) {
                $comments[] = new CommentDTO($comment);
            }

            //Retrieves User's comment
            foreach ($comments as $comment) {
                $userDAO = new UserDAO();
                $user = $userDAO->getUserById($comment->getIdUser());
                if (!empty($user)) {
                    $comment->setUserDTO($user);
                }
            }
        }

        return $comments;
    }

    // Gets All Comments for a Post
    public function getByPostId(int $postId, array $filters = null): ?array
    {
        $comments = [];

        // Retrieves posts on post id
        $query = "SELECT comment.* FROM `comment` JOIN user ON id_user = user.id  WHERE `id_post` = $postId";

        // Add filter on user's status
        if (isset($filters['userDeactivated'])) {
            $query .= ' AND user.is_deactivated = '.$filters['userDeactivated'];
        }

        // Add filter on comment's status
        if (!empty($filters['status'])) {
            $query .= " AND `status` = " .'"'.$filters['status'].'"';
        }

        // Get's data on db
        $req = $this->db->query($query);
        $data = $req->fetchAll(\PDO::FETCH_ASSOC);

        if (!empty($data)) {
            // Creates Comments Objects
            foreach ($data as $comment) {
                $comments[] = new CommentDTO($comment);
            }

            //Retrieves User's comment
            foreach ($comments as $comment) {
                $userDAO = new UserDAO();
                $user = $userDAO->getUserById($comment->getIdUser());
                if (!empty($user)) {
                    $comment->setUserDTO($user);
                }
            }
        }

        return $comments;
    }

    // Get comment by it's id
    public function getCommentById(int $id): ?CommentDTO
    {
        // Retrieves comment on id
        $req = $this->db->query('SELECT * FROM comment WHERE id = \''.$id.'\'');
        $comment = $req->fetch(\PDO::FETCH_ASSOC);

        // If comment doesn't find return null
        if (empty($comment)) {
            return null;
        }

        // Creates and return Comment's Object
        return new CommentDTO($comment);
    }

    // Creates a Comment
    public function save(CommentDTO $commentDTO): bool
    {
        // Checks if the comment already exists
        if (!empty($commentDTO->getId())) {
            // Prepares the request
            $req = $this->db->prepare('UPDATE comment SET content=:content, status=:status WHERE id = '.$commentDTO->getId());
            // Updates data
            $result = $req->execute(['content' => $commentDTO->getContent(), 'status' => $commentDTO->getStatus()]);
        } else {
            // Prepares the request
            $req = $this->db->prepare('INSERT INTO `comment`(`content`, `id_post`, `id_user`, `status`, `created_at`) VALUES(:content, :id_post, :id_user, :status, :createdAt)');
            // Inserts data
            $result = $req->execute(['content' => $commentDTO->getContent(), 'id_post' => $commentDTO->getIdPost(), 'id_user' => $commentDTO->getIdUser(), 'status' => $commentDTO->getStatus(), 'createdAt' => date('Y-m-d H:i:s')]);
        }

        return $result;
    }

    // Deletes a comment
    public function delete(CommentDTO $commentDTO): int
    {
        // Checks if it's a valid comment
        if (empty($commentDTO->getId())) {
            return null;
        }

        // Deletes data
        return $this->db->exec('DELETE FROM comment WHERE id = '.$commentDTO->getId());
    }
}