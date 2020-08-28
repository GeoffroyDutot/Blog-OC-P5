<?php


namespace App\DAO;


use App\DTO\PostDTO;

class PostDAO extends DAO {

    public function getAll() : array {
        $db = $this->connectDb();
        $posts = [];

        $req = $db->query('SELECT * FROM `post`');

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