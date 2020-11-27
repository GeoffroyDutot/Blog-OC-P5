<?php


namespace App\DAO;


use App\DTO\PostDTO;

class PostDAO extends DAO
{
    public function getAll($limit = null): array
    {
        //@TODO add filters and fusion getAllArchived
        $db = $this->connectDb();
        $posts = [];

        $query = 'SELECT * FROM `post` WHERE `is_archived` = 0 ORDER BY `created_at` DESC ';

        if ($limit) {
            $limit = 'LIMIT ' . $limit;
        }

        $req = $db->query($query . $limit);
        $data = $req->fetchAll(\PDO::FETCH_ASSOC);

        if (!$data) {
            return $posts;
        }

        foreach ($data as $post) {
            $posts[] = new PostDTO($post);
        }

        return  $posts;
    }

    public function getAllArchived($limit = null): array
    {
        $db = $this->connectDb();
        $posts = [];

        $query = 'SELECT * FROM `post` WHERE `is_archived` = 1 ORDER BY `created_at` DESC ';

        if ($limit) {
            $limit = 'LIMIT ' . $limit;
        }

        $req = $db->query($query . $limit);
        $data = $req->fetchAll(\PDO::FETCH_ASSOC);

        if (!$data) {
            return $posts;
        }

        foreach ($data as $post) {
            $posts[] = new PostDTO($post);
        }

        return  $posts;
    }

    public function getPostById(int $postId, bool $details = false): ?PostDTO
    {
        $db = $this->connectDb();

        $req = $db->query('SELECT * FROM post WHERE id = \''.$postId.'\' LIMIT 1');
        $post = $req->fetch(\PDO::FETCH_ASSOC);

        if (empty($post)) {
            return null;
        }

        $postDTO = new PostDTO($post);

        if ($details) {
            // Retrieves post comments
            $commentDAO = new CommentDAO();
            $filters = ['postId' => $postId];
            $comments = $commentDAO->getByPostId($filters);
            if (!empty($comments)) {
                $postDTO->setComments($comments);
            }
        }

        return $postDTO;
    }

    public function getPostBySlug(string $slug): ?PostDTO
    {
        $db = $this->connectDb();

        $req = $db->query("SELECT * FROM `post` WHERE slug = '$slug' LIMIT 1");
        $post = $req->fetch(\PDO::FETCH_ASSOC);

        if (empty($post)) {
            return null;
        }

        $postDTO = new PostDTO($post);

        //Retrieves post comments
        $commentDAO = new CommentDAO();
        $filters = ['postId' => $postDTO->getId(), 'userDeactivated' => 0, 'status' => 'validated'];
        $comments = $commentDAO->getByPostId($filters);
        if (!empty($comments)) {
            $postDTO->setComments($comments);
        }

        return $postDTO;
    }

    public function save(PostDTO $postDTO)
    {
        $db = $this->connectDb();

        if (!empty($postDTO->getId())) {
            $archivedAt = $postDTO->getArchivedAt() ? $postDTO->getArchivedAt()->format('Y-m-d H:i:s') : null;
            $req = $db->prepare('UPDATE post SET title=:title, slug=:slug, subtitle=:subtitle, updated_at=:updated_at, content=:content, resume=:resume, picture=:picture, archived_at=:archived_at, is_archived=:is_archived WHERE id = \''.$postDTO->getId().'\'');
            $result = $req->execute(['title' => $postDTO->getTitle(), 'slug' => $postDTO->getSlug(), 'subtitle' => $postDTO->getSubtitle(), 'updated_at' => date('Y-m-d H:i:s'), 'content' => $postDTO->getContent(), 'resume' => $postDTO->getResume(), 'picture' => $postDTO->getPicture(), 'archived_at' => $archivedAt, 'is_archived' => $postDTO->getIsArchived()]);
        } else {
            $req = $db->prepare('INSERT INTO `post`(`title`, `slug`, `subtitle`, `created_at`, `content`, `resume`, `picture`, `is_archived`) VALUES(:title, :slug, :subtitle, :created_at, :content, :resume, :picture, :is_archived)');
            $result = $req->execute(['title' => $postDTO->getTitle(), 'slug' => $postDTO->getSlug(), 'subtitle' => $postDTO->getSubtitle(), 'created_at' => $postDTO->getCreatedAt()->format('Y-m-d H:i:s'), 'content' => $postDTO->getContent(), 'resume' => $postDTO->getResume(), 'picture' => $postDTO->getPicture(), 'is_archived' => $postDTO->getIsArchived()]);

            if (!empty($result)) {
                $postDTO->setId($db->lastInsertId());
            }
        }

        return $result;
    }

    public function delete(PostDTO $postDTO)
    {
        $db = $this->connectDb();

        if (empty($postDTO->getId())) {
            return null;
        }

        if ($postDTO->getComments()) {
            foreach ($postDTO->getComments() as $commentDTO) {
                $commentDAO = new CommentDAO();
                $commentDAO->delete($commentDTO);
            }
        }

        return $db->exec('DELETE FROM post WHERE id = '.$postDTO->getId());
    }
}