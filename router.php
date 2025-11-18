<?php

require_once 'libs/router/router.php';
require_once 'app/controllers/film.api.controller.php';

//instancio el router

$router = new Router();

//defino los endpoints
$router->addRoute('movies',        'GET',        'FilmApiController',          'getFilms');
$router->addRoute('movies/:id',    'GET',        'FilmApiController',          'getFilmById');
$router->addRoute('movies/:id',    'PUT',        'FilmApiController',          'updateFilm');
$router->addRoute('movies',        'POST',       'FilmApiController',          'postFilm'); 
$router->addRoute('movies/:id',    'DELETE',     'FilmApiController',          'deleteFilm');



$router->route($_GET['resource'], $_SERVER['REQUEST_METHOD']);
