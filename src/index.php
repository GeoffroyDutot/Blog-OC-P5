<?php
require 'vendor/autoload.php';


$router = new App\Router\Router($_GET['url']);

$router->get('/', "Home#index");

$router->post('/contact', "Home#contact");

$router->get('/articles', "Post#index");

$router->get('/article/:slug', "Post#show")->with('slug', '([a-z\-0-9]+)');

$router->get('articles/:id', "Articles#show");

$router->get('/inscription',"User#register");

$router->post('/ajout-utilisateur',"User#addUser");

$router->get('/connexion',"User#login");

$router->post('/verification-connexion',"User#authenticate");

$router->get('/deconnexion',"User#logout");

/// ADMIN

$router->get('/admin/tableau-de-bord', "Admin#index");

$router->get('/admin/utilisateurs', "Admin#listUsers");

$router->get('/admin/articles', "Admin#listPosts");

$router->run();