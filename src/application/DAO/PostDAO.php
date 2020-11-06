<?php


namespace App\DAO;


use App\DTO\PostDTO;

class PostDAO extends DAO {

    public function getAll($limit = null) : array {
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

    public function getAllArchived($limit = null) : array {
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

    public function getPostById(int $postId) : PostDTO
    {
        $db = $this->connectDb();

        $req = $db->query('SELECT * FROM post WHERE id = \''.$postId.'\'');
        $post = $req->fetch(\PDO::FETCH_ASSOC);

        if (empty($post)) {
            return null;
        }

        return new PostDTO($post);
    }

    public function getPostBySlug(string $slug) : PostDTO {
        $db = $this->connectDb();

        $req = $db->prepare('SELECT * FROM `post` WHERE `slug` = :slug LIMIT 1');
        $req->execute(['slug' => $slug]);
        $post = $req->fetch(\PDO::FETCH_ASSOC);
        $postDTO = new PostDTO($post);

        return $postDTO;
    }

    public function save(PostDTO $postDTO)
    {
        $db =$this->connectDb();

        $postId = $postDTO->getId();
        if (!empty($postId)) {
            $archivedAt = $postDTO->getArchivedAt() ? $postDTO->getArchivedAt()->format('Y-m-d H:i:s') : null;
            $req = $db->prepare('UPDATE post SET title=:title, slug=:slug, subtitle=:subtitle, updated_at=:updated_at, content=:content, resume=:resume, picture=:picture, archived_at=:archived_at, is_archived=:is_archived WHERE id = \''.$postId.'\'');
            $result = $req->execute(['title' => $postDTO->getTitle(), 'slug' => $postDTO->getSlug(), 'subtitle' => $postDTO->getSubtitle(), 'updated_at' => date('Y-m-d H:i:s'), 'content' => $postDTO->getContent(), 'resume' => $postDTO->getResume(), 'picture' => $postDTO->getPicture(), 'archived_at' => $archivedAt, 'is_archived' => $postDTO->getIsArchived()]);
        } else {
            $req = $db->prepare('INSERT INTO `post`(`title`, `slug`, `subtitle`, `created_at`, `updated_at`, `content`, `resume`, `picture`, `archived_at`, `is_archived`) VALUES(:title, :slug, :subtitle, :created_at, :updated_at, :content, :resume, :picture, :archived_at, :is_archived)');
            $result = $req->execute(['title' => $postDTO->getTitle(), 'slug' => $postDTO->getSlug(), 'subtitle' => $postDTO->getSubtitle(), 'created_at' => $postDTO->getCreatedAt()->format('Y-m-d H:i:s'), 'updated_at' => $postDTO->getUpdatedAt()->format('Y-m-d H:i:s'), 'content' => $postDTO->getContent(), 'resume' => $postDTO->getResume(), 'picture' => $postDTO->getPicture(), 'archived_at' => $postDTO->getArchivedAt()->format('Y-m-d H:i:s'), 'is_archived' => $postDTO->getIsArchived()]);

            if (!empty($result)) {
                $postDTO->setId($db->lastInsertId());
            }
        }

        return $result;
    }
}