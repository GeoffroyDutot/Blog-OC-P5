<?php
require 'vendor/autoload.php';

$router = new App\Router\Router($_GET['url']);

//////
// COMMON
/////

// COMMON
$router->get('/', "Home#index");
$router->post('/contact', "Home#contact");

// POST
$router->get('/articles', "Post#index");
$router->get('/article/:slug', "Post#show")->with('slug', '([a-z\-0-9]+)');
$router->post('/article/:id/commentaire', "Post#submitComment")->with('id', '([0-9]+)');

// USER
$router->get('/inscription',"User#register");
$router->post('/ajout-utilisateur',"User#addUser");
$router->get('/connexion',"User#login");
$router->post('/verification-connexion',"User#authenticate");
$router->get('/deconnexion',"User#logout");

/////
// ADMIN
/////

// COMMON
$router->get('/admin/tableau-de-bord', "Admin#index");

// POST
$router->get('/admin/articles', "Admin#listPosts");

// COMMENT
$router->get('/admin/commentaires', "Admin#listComments");

// USER
$router->get('/admin/utilisateurs', "Admin#listUsers");

// ABOUT ME
$router->get('/admin/a-propos', 'Admin#aboutMe');
$router->post('/admin/modifier-a-propos', 'Admin#editAboutMe');

/////
// API
/////

// POST
$router->post('/admin/articles/archiver', 'Api#archivePost');

// COMMENT
$router->post('/admin/commentaires/valider', 'Api#validateComment');
$router->post('/admin/commentaires/refuser', 'Api#unvalidateComment');

$router->run();