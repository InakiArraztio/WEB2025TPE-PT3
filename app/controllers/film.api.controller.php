<?php

require_once 'app/models/film.model.php';
require_once 'app/models/gender.model.php';

class FilmApiController {
    private $model;
    private $genderModel;

    function __construct() {
        $this->model = new FilmsModel();
        $this->genderModel = new GenderModel();
        //sin vista uso el $res->json del router
    }

     function getFilms($req, $res) {
        // parametros de consulta (query params)
        $orderBy = $req->query->orderBy ?? 'titulo'; // campo de orden
        //strtoupper convierto una cadena de texto a mayusculas
        $sort = strtoupper($req->query->sort ?? 'ASC'); //ASC o DESC
        $limit = isset($req->query->limit) ? (int)$req->query->limit : null;
        $page = isset($req->query->page) ? (int)$req->query->page : null;

        // campos permitidos para ordenar
        $campos = ['id_pelicula', 'titulo', 'anio', 'rating', 'id_genero'];
        $camposPermitidos = ['ASC', 'DESC'];

        if (!in_array($orderBy, $campos)) {
            $orderBy = 'titulo';
        }

        if (!in_array($sort, $camposPermitidos)) {
            $sort = 'ASC';
        }

        // Límite y paginación
        if (isset($req->query->limit) && is_numeric($req->query->limit)) {
            $limit = (int) $req->query->limit; // el int,hacer que se haga int por si no llega a  ser de ese tipo
        }

        if (isset($req->query->page) && is_numeric($req->query->page)) {
            $page = (int) $req->query->page;
        }

        // Si hay página pero no límite, se usa 5 por defecto
        if ($limit === null && $page !== null) {
            $limit = 5;
        }

        $films = $this->model->getMovies($orderBy, $sort, $limit, $page);

        if ($films) {
            return $res->json($films, 200);
        } else {
            return $res->json(["error" => "No hay películas registradas"], 404);

        }
    }

    function getFilmById($req, $res) {
        $idFilm = $req->params->id;
        $film = $this->model->getMovie($idFilm);

        if(!$film) {
            return $res->json("La pelicula con el id=$idFilm no existe", 404);
        }

        return $res->json($film);
    }

    //PUT /api/movies/:id TP 3
    function updateFilm($req, $res) {
        //obtengo el id desde el params
        $idFilm = $req->params->id;

        //verifico que exista
        $film = $this->model->getMovie($idFilm);
        if(!$film) {
            return $res->json(["Error" => "La pelicula con el id=$idFilm no existe."], 404);
        }

        //Valido todos los campos requeridos en el body 
        if(empty($req->body->titulo) || empty($req->body->anio) || empty($req->body->rating) || empty($req->body->id_genero) || empty($req->body->genero)) {
            return $res->json(["error" => "Faltan datos obligatorios (titulo, anio, rating,genero o id_genero)"], 400);
        }

        //Obtengo los datos
        $titulo = $req->body->titulo;
        $anio = $req->body->anio;
        $rating = $req->body->rating;
        $id_genero = $req->body->id_genero;

        // validar rango mínimo 
        if ($anio <= 1800 || $anio > (int)date("Y") + 1) {
            return $res->json(["error" => "Año inválido"], 400);
        }

        // determino el id del genero
        if (!empty($req->body->genero)) {
            $nombreGenero = trim($req->body->genero);
            $gender = $this->genderModel->getByName($nombreGenero);
            if ($gender)
                $id_genero = $gender->id_genero;
        } elseif (!empty($req->body->id_genero)) {
            $id_genero = (int)$req->body->id_genero;
            $gender = $this->genderModel->getGenderById($id_genero);
        }

        // verifico que exista el genero
        $genero = $this->genderModel->getGenderById($id_genero);
        if (!$genero) {
            return $res->json(["error" => "El genero ingresado no existe."], 400);
        }

        // verifico que no exista otra película con mismo título y año
        $duplicado = $this->model->getFilmByTitleAndYear($titulo, $anio);
        if($duplicado && $duplicado->id_pelicula != $idFilm) {
            return $res->json(["error" => "Ya existe otra película con ese título y año."], 400);
        }

        //Acutalizo el modelo
        $this->model->updateFilm($idFilm,$titulo,$anio,$rating,$id_genero);

        $film = $this->model->getMovie($idFilm);

        //devolver solo campos "editables"
        $datos = [
            "titulo" => $film->titulo,
            "anio" => $film->anio,
            "rating" => $film->rating,
            "genero" => $film->genero
        ];

        return $res->json($datos, 200);  
    }

    function deleteFilm($req, $res) {
        $idFilm = $req->params->id;
        $film = $this->model->getMovie($idFilm);

        if (!$film) {
            return $res->json("La pelicula con el id=$idFilm no existe", 404);
        }

        $this->model->deleteFilm($idFilm);

        return $res->json("La pelicula con el id=$idFilm se eliminó", 200);

    }

    function postFilm($req,$res) {
        if(!isset($req->body->titulo) || !isset($req->body->anio) || !isset($req->body->rating) || !isset($req->body->id_genero)) {
            return $res->json(["error" => "Faltan datos obligatorios (titulo, anio, rating, id_genero)"], 400);
        }

        $titulo= $req->body->titulo;
        $anio=$req->body->anio;
        $rating=$req->body->rating;
        $id_genero=$req->body->id_genero;
        //como poster no es obligatorio se usa "", para que el campo pueda estar vacio
        $poster = $req->body->poster ?? ""; 

        // valido que el genero exista
        $genero = $this->genderModel->getGenderById($id_genero);
        if (!$genero) {
            return $res->json(["error" => "El género ingresado no existe"], 400);
        }

        $id = $this->model->postFilm($titulo, $anio, $rating, $id_genero, $poster);
        return $res->json(["id" => $id, "mensaje" => "Película creada exitosamente"], 201);
    }

}