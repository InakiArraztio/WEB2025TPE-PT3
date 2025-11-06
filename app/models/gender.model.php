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
}
