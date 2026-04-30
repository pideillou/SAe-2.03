<?php

require('model.php');

function readMoviesController($age = 0)
{
    return getAllMovies($age);
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

function readFeaturedMoviesController()
{
    $age = isset($_REQUEST['age']) ? (int) $_REQUEST['age'] : 0;
    if ($age < 0 || $age > 18) {
        $age = 0;
    }

    $featured = getFeaturedMovies($age);
    if ($featured === false) {
        return ['error' => 'Erreur lors du chargement des films mis en avant.'];
    }

    return $featured;
}

function addProfileController()
{
    return saveProfileController();
}

function saveProfileController()
{
    if (!isset($_POST['name']) || trim((string) $_POST['name']) === '') {
        return ['error' => 'Le nom du profil est obligatoire.'];
    }
    if (!isset($_POST['min_age']) || $_POST['min_age'] === '') {
        return ['error' => 'La restriction d\'age est obligatoire.'];
    }

    $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : 0;
    $name = trim($_POST['name']);
    $minAge = (int) $_POST['min_age'];
    $image = isset($_POST['image']) && trim((string) $_POST['image']) !== '' ? trim($_POST['image']) : null;

    if ($id < 0) {
        return ['error' => 'Identifiant de profil invalide.'];
    }
    if ($minAge < 0 || $minAge > 18) {
        return ['error' => 'La restriction d\'age doit etre comprise entre 0 et 18.'];
    }

    $savedId = saveProfile($id, $name, $image, $minAge);
    if ($savedId === false) {
        return ['error' => 'Erreur serveur lors de la sauvegarde du profil.'];
    }

    if ($id > 0) {
        return ['success' => 'Le profil a ete modifie avec succes.', 'id' => $savedId];
    }

    return ['success' => 'Le profil a ete ajoute avec succes.', 'id' => $savedId];
}

function readProfilesController()
{
    return getAllProfiles();
}

function addFavoriteController()
{
    if (!isset($_POST['id_profile']) || !isset($_POST['id_movie'])) {
        return ['error' => 'Paramètres manquants.'];
    }

    $idProfile = (int) $_POST['id_profile'];
    $idMovie = (int) $_POST['id_movie'];

    if ($idProfile < 1 || $idMovie < 1) {
        return ['error' => 'Identifiants invalides.'];
    }

    $ok = addFavorite($idProfile, $idMovie);
    if ($ok === false) {
        return ['error' => 'Erreur lors de l\'ajout aux favoris.'];
    }

    return ['success' => 'Le film a ete ajoute a vos favoris.'];
}

function removeFavoriteController()
{
    if (!isset($_POST['id_profile']) || !isset($_POST['id_movie'])) {
        return ['error' => 'Paramètres manquants.'];
    }

    $idProfile = (int) $_POST['id_profile'];
    $idMovie = (int) $_POST['id_movie'];

    if ($idProfile < 1 || $idMovie < 1) {
        return ['error' => 'Identifiants invalides.'];
    }

    $ok = removeFavorite($idProfile, $idMovie);
    if ($ok === false) {
        return ['error' => 'Erreur lors de la suppression des favoris.'];
    }

    return ['success' => 'Le film a ete supprime de vos favoris.'];
}

function readFavoritesController()
{
    if (!isset($_REQUEST['id_profile'])) {
        return ['error' => 'Identifiant du profil manquant.'];
    }

    $idProfile = (int) $_REQUEST['id_profile'];
    if ($idProfile < 1) {
        return ['error' => 'Identifiant de profil invalide.'];
    }

    $favorites = getFavorites($idProfile);
    if ($favorites === false) {
        return ['error' => 'Erreur lors du chargement des favoris.'];
    }

    return $favorites;
}

function getStatisticsController()
{
    $stats = [];

    // Total profiles
    $profileCount = getTotalProfiles();
    $stats['total_profiles'] = $profileCount ? intval($profileCount['total_profiles']) : 0;

    // Total movies
    $movieCount = getTotalMovies();
    $stats['total_movies'] = $movieCount ? intval($movieCount['total_movies']) : 0;

    // Average favorites per profile
    $avgFav = getAverageFavoritesPerProfile();
    $stats['avg_favorites_per_profile'] = $avgFav ? floatval($avgFav['avg_favorites']) : 0;

    // Most favorited movie
    $mostFav = getMostFavoritedMovie();
    $stats['most_favorited_movie'] = $mostFav ? [
        'id' => intval($mostFav['id']),
        'name' => $mostFav['name'],
        'favorite_count' => intval($mostFav['favorite_count'])
    ] : null;

    // Most popular category
    $popularCat = getMostPopularCategory();
    $stats['most_popular_category'] = $popularCat ? [
        'name' => $popularCat['name'],
        'favorite_count' => intval($popularCat['favorite_count'])
    ] : null;

    return $stats;
}