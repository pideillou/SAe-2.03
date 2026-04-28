<?php

require('model.php');

function readMoviesController()
{
    return getAllMovies();
}

function addMovieController()
{
    $required = ['name', 'director', 'year', 'length', 'description', 'id_category', 'image', 'min_age'];
    foreach ($required as $field) {
        if (!isset($_POST[$field]) || trim((string) $_POST[$field]) === '') {
            return false;
        }
    }

    $name = trim($_POST['name']);
    $director = trim($_POST['director']);
    $year = (int) $_POST['year'];
    $length = (int) $_POST['length'];
    $description = trim($_POST['description']);
    $idCategory = (int) $_POST['id_category'];
    $image = trim($_POST['image']);
    $trailer = isset($_POST['trailer']) ? trim((string) $_POST['trailer']) : '';
    $minAge = (int) $_POST['min_age'];

    if ($year < 1888 || $year > 2100) {
        return false;
    }
    if ($length < 1) {
        return false;
    }
    if ($idCategory < 1) {
        return false;
    }
    if ($minAge < 0 || $minAge > 18) {
        return false;
    }

    $ok = addMovie($name, $director, $year, $length, $description, $idCategory, $image, $trailer, $minAge);
    if ($ok === false) {
        return false;
    }

    return ['success' => 'Le film a ete ajoute avec succes.'];
}

function readMovieDetailController()
{
    if (!isset($_REQUEST['id']) || empty($_REQUEST['id'])) {
        return false;
    }

    $id = (int) $_REQUEST['id'];
    if ($id < 1) {
        return false;
    }

    $movie = getMovieDetail($id);
    if ($movie === false || $movie === null) {
        return false;
    }

    return $movie;
}

function addProfileController()
{
    if (!isset($_POST['name']) || trim((string) $_POST['name']) === '') {
        return false;
    }
    if (!isset($_POST['min_age']) || $_POST['min_age'] === '') {
        return false;
    }

    $name = trim($_POST['name']);
    $minAge = (int) $_POST['min_age'];
    $image = isset($_POST['image']) && trim((string) $_POST['image']) !== '' ? trim($_POST['image']) : null;

    if ($minAge < 0 || $minAge > 18) {
        return false;
    }

    $ok = addProfile($name, $image, $minAge);
    if ($ok === false) {
        return false;
    }

    return ['success' => 'Le profil a ete ajoute avec succes.'];
}

function readProfilesController()
{
    return getAllProfiles();
}