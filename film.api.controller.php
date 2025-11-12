<?php

require_once 'app/models/film.model.php';
require_once 'app/models/gender.model.php';
require_once 'app/view/json.view.php';
/*

Implementar ordenamiento 
El endpoint :
GET /api/movies?sort=titulo&order=desc

sort: campo por le que ordenar (id_pelicula)
order: asc o desc

    function getFilms($sort = 'id_pelicula', $order = 'ASC') {
        $fields = ['id_pelicula', 'titulo', 'anio', 'rating'];
        $orders = ['ASC', 'DESC'];

        if(!in_array($sort,$fields)) $sort = 'id_pelicula';
        if(!in_array(strtoupper($order), $orders)) $order = 'ASC';

        $query = $this->db->prepare("SELECT * FROM pelicula ORBDER BY $sort $order");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }


*/

class FilmApiController {
    private $model;
    private $genderModel;

    function __construct() {
        $this->model = new FilmsModel();
        $this->genderModel = new GenderModel();
        $this->vista = new JSONVvista();
        //sin vista uso el $res->json del router
    }

public function getFilms($req, $res) {
    $orderBy = null;
    $sort = null;
    $limit = null;
    $page = null;
    $filter=null;
    $valor=null;
    // guardar, el valor porque el que esta preguntado anio, nombre
    // guardar el valor que te insetro por la url del campo 

    //Ordenamiento
    if (isset($req->query->orderBy)) {
        $orderBy = $req->query->orderBy;
    } else {
        $orderBy = 'titulo'; // valor por defecto
    }

    if (isset($req->query->sort)) {
        $sort = strtoupper($req->query->sort);
        if (!in_array($sort, ['ASC', 'DESC'])) {
            $sort = 'ASC';// valor por defecto
        }
    } else {
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

    //Llamada al modelo
    $films = $this->model->getMovies($orderBy, $sort, $limit, $page);

    // Respuesta
    if ($films) {
        return $this->vista->response($films, 200);
    } else {
        return $res->json(["error" => "ulas registradas"], 404);
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

        return $res->json("La pelicula con el id=$idFilm se eliminó", 204);

    }
    function postFilm($req, $res){
         if(empty($req->body->titulo) || empty($req->body->anio) || empty($req->body->rating) || empty($req->body->id_genero) || empty($req->body->poster)) {
            return $this->vista->response(["error" => "Faltan datos obligatorios (titulo, anio, rating,genero o id_genero)"], 400);
        }
        $genero = $this->genderModel->getGenderById($id_genero);
        if (!$genero) {
            return $this->vista->response(["error" => "El genero ingresado no existe."], 400);
        }
        $titulo= $req->body->titulo;
        $anio=$req->body->anio;
        $rating=$req->body->rating;
        $id_genero=$req->body->id_genero;
        $poster=$req->body->poster;
        
        $id = $this->genderModel->insertGender($titulo,$anio,$rating,$id_genero,$poster);
        if($id){
            return $this->vista->response($id,200);
        }
    } 
    /*
        function updateTask($req,$res) {
            $id_task = $req->params->id;
            $task = $this->model->get($id_task);

            if($task) {
                $titulo = $req->body->titulo;
                $descripcion = $req->body->descripcion;
                $finalizada = $req->body->finalizada;

                $tarea = $this->model->updateTask($id_task,$titulo,$descripcion,$finalizada);
                $res->json("Tarea id=$id_task actualizada con exito", 200);
            } else {
                $res->json("Task id=$id_task not found", 404);    
            }
        }
    */

}