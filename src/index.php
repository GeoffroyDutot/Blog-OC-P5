<?php
require 'vendor/autoload.php';

$router = new App\Router\Router($_GET['url']);

//////
// COMMON
/////

// COMMON
$router->get('/', "Home#index");
$router->post('/contact', "Home#contact");
$router->get('/page-introuvable', 'Error#show404');

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
$router->get('/admin/articles/nouveau', 'Admin#addPost');
$router->post('/admin/article', 'Admin#newPost');
$router->get('/admin/article/:id', 'Admin#editPost')->with('id', '([0-9]+)');
$router->post('/admin/article/modifier/:id', 'Admin#updatePost')->with('id', '([0-9]+)');

// COMMENT
$router->get('/admin/commentaires', "Admin#listComments");

// USER
$router->get('/admin/utilisateurs', "Admin#listUsers");
$router->get('/admin/utilisateur/:id', 'Admin#editUser')->with('id', '([0-9]+)');
$router->post('/admin/utilisateur/modifier/:id', 'Admin#updateUser')->with('id', '([0-9]+)');

// ABOUT ME
$router->get('/admin/a-propos', 'Admin#aboutMe');
$router->post('/admin/modifier-a-propos', 'Admin#editAboutMe');

/////
// API
/////

// POST
$router->post('/admin/articles/archiver', 'Api#archivePost');
$router->post('/admin/articles/desarchiver', 'Api#unarchivePost');
$router->delete('/admin/articles/supprimer/:id', "Api#deletePost")->with('id', '([0-9]+)');

// COMMENT
$router->post('/admin/commentaires/valider', 'Api#validateComment');
$router->post('/admin/commentaires/refuser', 'Api#unvalidateComment');

// USER
$router->put('/admin/utilisateurs/:id/desactiver', 'Api#deactivateUser')->with('id', '([0-9]+)');
$router->put('/admin/utilisateurs/:id/reactiver', 'Api#reactivateUser')->with('id', '([0-9]+)');

$router->run();