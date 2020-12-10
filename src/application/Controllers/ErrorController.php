<?php


namespace App\Controllers;


use App\DAO\AboutMeDAO;

class ErrorController extends Controller
{
    // Show 404 page
    public function show404()
    {
        // Set HTTP 404 status
        http_response_code(404);

        // Get AboutMe data
        $aboutMe = new AboutMeDAO();
        $aboutMe = $aboutMe->getAboutMe();

        // Set data from AboutMe infos
        $data = ['aboutMe' => $aboutMe];

        // Show 404 view
        $this->render('lost.html.twig', $data);
    }
}