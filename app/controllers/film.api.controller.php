<?php

require_once 'app/models/film.model.php';
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

    function __construct() {
        $this->model = new FilmsModel();
        //sin vista uso el $res->json del router
    }

     function getFilms($req, $res) {
        $orderBy = $req->query->orderBy ?? 'titulo';
        $sort = strtoupper($req->query->sort ?? 'ASC');
        $limit = isset($req->query->limit) ? (int)$req->query->limit : null;
        $page = isset($req->query->page) ? (int)$req->query->page : null;

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
        if(empty($req->body->titulo) || empty($req->body->anio) || empty($req->body->rating) || empty($req->body->id_genero)) {
            return $res->json(["error" => "Faltan datos obligatorios (titulo, anio, rating, id_genero)"], 400);
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

        //Acutalizo el modelo
        $this->model->updateFilm($idFilm,$titulo,$anio,$rating,$id_genero);

        $film = $this->model->getMovie($idFilm);
        return $res->json($film, 201);  
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