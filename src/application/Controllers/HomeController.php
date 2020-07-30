<?php

namespace App\Controllers;

use App\DAO\AboutMeDAO;

class HomeController extends Controller {

    public function index() {
        $data = [];
        $aboutMe = new AboutMeDAO();
        $aboutMe = $aboutMe->getAboutMe();
        $data['aboutMe'] = $aboutMe;

        $this->render('home.html.twig', $data);
    }
}
