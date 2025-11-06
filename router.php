<?php

require_once 'libs/router.php';
require_once 'app/controllers/film.api.controller.php';
//instancio el router

$router = new Router();

//defino los endpoints
$router->addRoute('movies',       'GET',        'FilmApiController',          'getFilms');
$router->addRoute('movie/:id',    'GET',        'FilmApiController',          'getFilmById');
$router->addRoute('movie/:id',    'PUT',        'FilmApiController',          'updateFilm'); 
$router->addRoute('movie/:id',    'DELETE',     'FilmApiController',          'deleteFilm');



$router->route($_GET['resource'], $_SERVER['REQUEST_METHOD']);
