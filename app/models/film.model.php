<?php

class FilmsModel {
    private $db;

    function __construct() {
        $this->db = $this->getConnection();
    }

    private function getConnection() {
        return new PDO('mysql:host=localhost;dbname=db_blockbuster;charset=utf8', 'root', ''); 
    }
    
    //3° ordenado por titulo o por año y paginado
    public function getMovies($orderBy = 'titulo', $sort = 'ASC', $limit = null, $page = null) {
        $allowedFields = ['id_pelicula', 'titulo', 'anio', 'rating'];
        $allowedOrder = ['ASC', 'DESC'];

        if (!in_array($orderBy, $allowedFields)) $orderBy = 'titulo';
        if (!in_array($sort, $allowedOrder)) $sort = 'ASC';

        $sql = "SELECT * FROM pelicula ORDER BY $orderBy $sort";

        // paginacion
        if ($limit && $page) {
            $offset = ($page - 1) * $limit;
            $sql .= " LIMIT $limit OFFSET $offset";
        } elseif ($limit) {
            //si se envia el limit pero no page
            $sql .= " LIMIT $limit";
        }

        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    //Listado ordenado: ordenado alfabeticamente 3° Entrega
    function getMoviesAlf($oderBy = 'titutlo', $order = 'ASC') {
        $fields = ['titulo', 'anio', 'rating', 'id_genero'];
        $orderAlf = ['ASC', 'DESC'];
        
        //in_array() sirve para verificar si un valor existe dentro de un arreglo.
        if(!in_array($oderBy,$fields)) $fields = 'titulo';
        if(!in_array($order,$orderAlf)) $order = 'ASC';

        $query = $this->db->prepare("SELECT * FROM pelicula ORDER BY $orderAlf $order");
        $query->execute();
        return $query->fetch(PDO::FETCH_OBJ);
    }

    function getMovie($id) {
        $query = $this->db->prepare('
                SELECT p.*, g.nombre AS genero
                FROM pelicula p
                JOIN genero g ON p.id_genero = g.id_genero
                WHERE p.id_pelicula = ?');
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_OBJ);
    }

    function insertFilm($titulo,$anio,$rating,$id_genero) {
        $query = $this->db->prepare('INSERT INTO pelicula (titulo,anio,rating,id_genero) VALUES (?,?,?,?)');
        $query->execute([$titulo,$anio,$rating,$id_genero]);
        return $this->db->lastInsertId();
    }

    //Funcion para que verificar no exista una pelicula por nombre y año
    function getFilmByTitleAndYear($titulo, $year) {
        $query = $this->db->prepare('SELECT * FROM pelicula WHERE titulo = ? AND anio = ?');
        $query->execute([$titulo, $year]);
        return $query->fetch(PDO::FETCH_OBJ);
    }
    
    function updateFilm($id,$titulo,$anio,$rating,$id_genero) {
        $query = $this->db->prepare('
                        UPDATE pelicula SET  
                        titulo = ?, anio = ?, rating = ?, id_genero = ?
                        WHERE id_pelicula = ?');
        $query->execute([$titulo,$anio,$rating,$id_genero,$id]);
    }

    function deleteFilm($id) {
        $query = $this->db->prepare('DELETE FROM pelicula WHERE id_pelicula = ?');
        $query->execute([$id]);
    }

    function getMoviesByGender($id) {
        $query = $this->db->prepare('SELECT * FROM pelicula WHERE id_genero = ?');
        $query->execute([$id]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    function postFilm($titulo,$anio,$rating,$id_genero,$poster){
        $query = $this->db->prepare('INSERT INTO pelicula (titulo, anio,rating,id_genero,poster) VALUES (?, ?, ?, ?, ?)');
        $query->execute([$titulo,$anio,$rating,$id_genero,$poster]);    
        $id = $this->db->lastInsertId();// ID de la último que fue agrego a la base de datos   
        return $id;
    }
}