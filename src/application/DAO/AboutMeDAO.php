<?php


namespace App\DAO;

use App\DAO\DAO;
use App\DTO\AboutMeDTO;


class AboutMeDAO extends DAO {

    public function getAboutMe(): AboutMeDTO {
    $db = $this->connectDB();

    $req = $db->query('SELECT * FROM `about_me` LIMIT 1');
    $aboutMe = $req->fetchObject('App\DTO\AboutMeDTO');
    return $aboutMe;
    }
}