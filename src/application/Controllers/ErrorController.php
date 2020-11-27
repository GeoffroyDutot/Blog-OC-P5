<?php


namespace App\Controllers;


use App\DAO\AboutMeDAO;

class ErrorController extends Controller
{
    public function show404()
    {
        http_response_code(404);

        $data = [];
        $aboutMe = new AboutMeDAO();
        $aboutMe = $aboutMe->getAboutMe();
        $data['aboutMe'] = $aboutMe;

        $this->render('lost.html.twig', $data);
    }
}