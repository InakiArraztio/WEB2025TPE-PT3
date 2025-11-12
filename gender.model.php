<?php
class GenderModel {
    private $db;

    function __construct() {
        $this->db = $this->getConnection();
    }

    private function getConnection() {
        return new PDO('mysql:host=localhost;dbname=db_blockbuster;charset=utf8', 'root', ''); 
    }

    function getGender() {
        $query = $this->db->prepare('SELECT * FROM genero');
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    function getGenderById($id) {
        $query = $this->db->prepare('SELECT * FROM genero WHERE id_genero = ?');
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_OBJ);
    }

    function getByName($nombre) {
        $query = $this->db->prepare('SELECT * FROM genero WHERE nombre = ?');
        $query->execute([$nombre]);
        return $query->fetch(PDO::FETCH_OBJ);
    }

    function insertGender($nombre) {
        $query = $this->db->prepare('INSERT INTO genero (nombre) VALUES (?)');
        $query->execute([$nombre]);
    }

    function updateGender($id, $nombre) {
        $query = $this->db->prepare('UPDATE genero SET nombre = ? WHERE id_genero = ?');
        $query->execute([$nombre, $id]);
    }

    function deleteGender($id) {
        $query = $this->db->prepare('DELETE FROM genero WHERE id_genero = ?');
        $query->execute([$id]);
    }

    function filmsGender($id) {
        $query = $this->db->prepare('
            SELECT p.*, g.nombre AS genero
            FROM pelicula p
            JOIN genero g ON p.id_genero = g.id_genero
            WHERE p.id_genero = ?
        ');
        $query->execute([$id]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    function postFilm($titulo,$anio,$rating,$id_genero,$poster){
    $query = $this->db->prepare('INSERT INTO pelicula (titulo, anio,rating,id_genero,poster) VALUES (?, ?, ?, ?)');
        $query->execute([$titulo,$anio,$rating,$id_genero,$poster]);    
        $id = $this->db->lastInsertId();// ID de la último que fue agrego a la base de datos   
        return $id;
    }

    // funcion para que devuelva el id del genero dado su nombre y me permite cambiar de forma directa el nombe del genero
    function getIdByName($nombre) {
        $query = $this->db->prepare('SELECT id_genero FROM genero WHERE nombre = ?');
        $query->execute([$nombre]);
        $res = $query->fetch(PDO::FETCH_OBJ);
        return $res ? $res->id_genero : null;
    }

    function getMovies($orderBy, $sort, $limit, $page){
        $sql  = 'SELECT * From pelicula';
        
        if($page !== null){
            $pag= $page - 1 *$limit;

            $sql .= ' LIMIT ' . $limit;
            $sql .= ' OFFSET ' . $pag;
        }
        $query = $this->db->prepare($sql);
        $peli = $query-fetchAll(PDO::FETCH_OBJ);
        return $peli;
    }
}
