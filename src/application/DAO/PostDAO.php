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


    public function getPostBySlug(string $slug) : PostDTO {
        $db = $this->connectDb();

        $req = $db->prepare('SELECT * FROM `post` WHERE `slug` = :slug LIMIT 1');
        $req->execute(['slug' => $slug]);
        $post = $req->fetch(\PDO::FETCH_ASSOC);
        $postDTO = new PostDTO($post);

        return $postDTO;
    }
}