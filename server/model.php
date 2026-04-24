<?php
define("HOST", "localhost");
define("DBNAME", "pideill2");
define("DBLOGIN", "pideill2");
define("DBPWD", "pideill2");

function getAllMovies() {
  $cnx = new PDO("mysql:host=" . HOST . ";dbname=" . DBNAME, DBLOGIN, DBPWD);
  $sql = "SELECT m.id, m.name, m.image, m.description, m.director, m.year, m.length, m.trailer, m.min_age, COALESCE(c.name, 'Sans catégorie') AS category FROM Movie m LEFT JOIN Category c ON m.id_category = c.id ORDER BY category, m.name";
  $stmt = $cnx->prepare($sql);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_OBJ);
}

function addMovie($name, $director, $year, $length, $description, $id_category, $image, $trailer, $min_age) {
  try {
    $cnx = new PDO("mysql:host=" . HOST . ";dbname=" . DBNAME, DBLOGIN, DBPWD);
    $sql = "INSERT INTO Movie (name, director, year, length, description, id_category, image, trailer, min_age) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $cnx->prepare($sql);
    $stmt->execute([$name, $director, $year, $length, $description, $id_category, $image, $trailer, $min_age]);
    return true;
  } catch (Exception $e) {
    return false;
  }
}

function getMovieDetail($id) {
  $cnx = new PDO("mysql:host=" . HOST . ";dbname=" . DBNAME, DBLOGIN, DBPWD);
  $sql = "SELECT m.id, m.name, m.image, m.description, m.director, m.year, m.length, m.trailer, m.min_age, c.name as category FROM Movie m LEFT JOIN Category c ON m.id_category = c.id WHERE m.id = ?";
  $stmt = $cnx->prepare($sql);
  $stmt->execute([$id]);
  return $stmt->fetch(PDO::FETCH_OBJ);
}

function addProfile($name, $image, $min_age) {
  try {
    $cnx = new PDO("mysql:host=" . HOST . ";dbname=" . DBNAME, DBLOGIN, DBPWD);
    $sql = "INSERT INTO Profile (name, image, min_age) VALUES (?, ?, ?)";
    $stmt = $cnx->prepare($sql);
    $stmt->execute([$name, $image, $min_age]);
    return true;
  } catch (Exception $e) {
    return false;
  }
}

function getAllProfiles() {
  try {
    $cnx = new PDO("mysql:host=" . HOST . ";dbname=" . DBNAME, DBLOGIN, DBPWD);
    $sql = "SELECT id, name, image, min_age FROM Profile ORDER BY name ASC";
    $stmt = $cnx->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  } catch (Exception $e) {
    return false;
  }
}