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
}