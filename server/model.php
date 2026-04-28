<?php

define('HOST', 'localhost');
define('DBNAME', 'pideill2');
define('DBLOGIN', 'pideill2');
define('DBPWD', 'pideill2');

function getConnection()
{
    $dsn = 'mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=utf8';
    return new PDO($dsn, DBLOGIN, DBPWD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    ]);
}

function getAllMovies()
{
    try {
        $cnx = getConnection();
        $sql = "SELECT m.id, m.name, m.image, m.description, m.director, m.year, m.length, m.trailer, m.min_age, COALESCE(c.name, 'Sans categorie') AS category
                FROM Movie m
                LEFT JOIN Category c ON m.id_category = c.id
                ORDER BY category, m.name";
        $stmt = $cnx->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return false;
    }
}

function addMovie($name, $director, $year, $length, $description, $idCategory, $image, $trailer, $minAge)
{
    try {
        $cnx = getConnection();
        $sql = 'INSERT INTO Movie (name, director, year, length, description, id_category, image, trailer, min_age)
                VALUES (:name, :director, :year, :length, :description, :id_category, :image, :trailer, :min_age)';
        $stmt = $cnx->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':director', $director);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->bindParam(':length', $length, PDO::PARAM_INT);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id_category', $idCategory, PDO::PARAM_INT);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':trailer', $trailer);
        $stmt->bindParam(':min_age', $minAge, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    } catch (Exception $e) {
        return false;
    }
}

function getMovieDetail($id)
{
    try {
        $cnx = getConnection();
        $sql = 'SELECT m.id, m.name, m.image, m.description, m.director, m.year, m.length, m.trailer, m.min_age, c.name AS category
                FROM Movie m
                LEFT JOIN Category c ON m.id_category = c.id
                WHERE m.id = :id';
        $stmt = $cnx->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    } catch (Exception $e) {
        return false;
    }
}

function addProfile($name, $image, $minAge)
{
    try {
        $cnx = getConnection();
        $sql = 'INSERT INTO Profile (name, image, min_age)
                VALUES (:name, :image, :min_age)';
        $stmt = $cnx->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':min_age', $minAge, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    } catch (Exception $e) {
        return false;
    }
}

function getAllProfiles()
{
    try {
        $cnx = getConnection();
        $sql = 'SELECT id, name, image, min_age FROM Profile ORDER BY name';
        $stmt = $cnx->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return false;
    }
}