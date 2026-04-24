<?php
/**
 * Ce fichier contient toutes les fonctions qui réalisent des opérations
 * sur la base de données, telles que les requêtes SQL pour insérer, 
 * mettre à jour, supprimer ou récupérer des données.
 */

/**
 * Définition des constantes de connexion à la base de données.
 *
 * HOST : Nom d'hôte du serveur de base de données, ici "localhost".
 * DBNAME : Nom de la base de données
 * DBLOGIN : Nom d'utilisateur pour se connecter à la base de données.
 * DBPWD : Mot de passe pour se connecter à la base de données.
 */
define("HOST", "localhost");
define("DBNAME", "pideill2");
define("DBLOGIN", "pideill2");
define("DBPWD", "pideill2");


function getAllMovies(){
    // Connexion à la base de données
    $cnx = new PDO("mysql:host=".HOST.";dbname=".DBNAME, DBLOGIN, DBPWD);
    // Requête SQL pour récupérer les films avec leur catégorie
    $sql = "SELECT m.id, m.name, m.image, m.description, m.director, m.year, m.length, m.trailer, m.min_age, COALESCE(c.name, 'Sans catégorie') AS category FROM Movie m LEFT JOIN Category c ON m.id_category = c.id ORDER BY category, m.name";
    // Prépare la requête SQL
    $stmt = $cnx->prepare($sql);
    // Exécute la requête SQL
    $stmt->execute();
    // Récupère les résultats de la requête sous forme d'objets
    $res = $stmt->fetchAll(PDO::FETCH_OBJ);
    return $res; // Retourne les résultats
}

function addMovie($name, $director, $year, $length, $description, $id_category, $image, $trailer, $min_age) {
    try {
        $cnx = new PDO("mysql:host=".HOST.";dbname=".DBNAME, DBLOGIN, DBPWD);
        $sql = "INSERT INTO Movie (name, director, year, length, description, id_category, image, trailer, min_age) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $cnx->prepare($sql);
        $stmt->execute([$name, $director, $year, $length, $description, $id_category, $image, $trailer, $min_age]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function getMovieDetail($id) {
    $cnx = new PDO("mysql:host=".HOST.";dbname=".DBNAME, DBLOGIN, DBPWD);
    $sql = "SELECT m.id, m.name, m.image, m.description, m.director, m.year, m.length, m.trailer, m.min_age, c.name as category FROM Movie m LEFT JOIN Category c ON m.id_category = c.id WHERE m.id = ?";
    $stmt = $cnx->prepare($sql);
    $stmt->execute([$id]);
    $res = $stmt->fetch(PDO::FETCH_OBJ);
    return $res;
}