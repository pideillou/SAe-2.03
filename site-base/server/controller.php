<?php

require_once(__DIR__ . '/model.php');

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

function searchMoviesController()
{
    if (!isset($_REQUEST['query']) || trim((string) $_REQUEST['query']) === '') {
        return false;
    }

    $query = trim($_REQUEST['query']);
    $age = isset($_REQUEST['age']) ? (int) $_REQUEST['age'] : 0;
    if ($age < 0 || $age > 18) {
        $age = 0;
    }

    $results = searchMovies($query, $age);
    if ($results === false) {
        return ['error' => 'Erreur lors de la recherche.'];
    }

    return $results;
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

function updateMovieFeaturedController()
{
    if (!isset($_POST['id_movie']) || !isset($_POST['is_featured'])) {
        return ['error' => 'Paramètres manquants.'];
    }

    $idMovie = (int) $_POST['id_movie'];
    $isFeatured = (int) $_POST['is_featured'];

    if ($idMovie < 1) {
        return ['error' => 'Identifiant de film invalide.'];
    }

    if ($isFeatured !== 0 && $isFeatured !== 1) {
        return ['error' => 'Statut invalide.'];
    }

    $ok = updateMovieFeatured($idMovie, $isFeatured);
    if ($ok === false) {
        return ['error' => 'Erreur serveur lors de la modification du statut.'];
    }

    $statusText = $isFeatured ? 'ajouté' : 'retiré';
    return ['success' => 'Le film a été ' . $statusText . ' des films mis en avant.'];
}

function addRatingController()
{
    if (!isset($_POST['id_profile']) || !isset($_POST['id_movie']) || !isset($_POST['score'])) {
        return ['error' => 'Paramètres manquants.'];
    }

    $idProfile = (int) $_POST['id_profile'];
    $idMovie = (int) $_POST['id_movie'];
    $score = (int) $_POST['score'];

    if ($idProfile < 1 || $idMovie < 1) {
        return ['error' => 'Identifiants invalides.'];
    }
    if ($score < 0 || $score > 10) {
        return ['error' => 'La note doit etre comprise entre 0 et 10.'];
    }

    $already = isRated($idProfile, $idMovie);
    if ($already === true) {
        return ['error' => 'Vous avez deja note ce film.'];
    }

    $ok = addRating($idProfile, $idMovie, $score);
    if ($ok === false) {
        return ['error' => 'Erreur serveur lors de l\'enregistrement de la note.'];
    }
    if ($ok === 0) {
        return ['error' => 'Vous avez deja note ce film.'];
    }

    return ['success' => 'Votre note a ete enregistree.'];
}

function getMovieRatingController()
{
    if (!isset($_REQUEST['id_movie']) || empty($_REQUEST['id_movie'])) {
        return ['error' => 'Parametre id_movie manquant.'];
    }

    $idMovie = (int) $_REQUEST['id_movie'];
    if ($idMovie < 1) {
        return ['error' => 'Identifiant de film invalide.'];
    }

    $avg = getMovieAverageRating($idMovie);
    if ($avg === false) {
        return ['error' => 'Erreur lors du calcul de la note moyenne.'];
    }

    $response = [
        'average' => isset($avg['avg_score']) ? floatval($avg['avg_score']) : 0,
        'count' => isset($avg['cnt']) ? intval($avg['cnt']) : 0,
    ];

    // optionally include user score if id_profile provided
    if (isset($_REQUEST['id_profile']) && (int)$_REQUEST['id_profile'] > 0) {
        $user = getUserRating((int)$_REQUEST['id_profile'], $idMovie);
        if ($user === false) {
            return ['error' => 'Erreur lors de la recuperation de la note utilisateur.'];
        }
        $response['user_score'] = $user === null ? null : intval($user);
    }

    return $response;
}

function addCommentController()
{
    if (!isset($_POST['id_profile']) || !isset($_POST['id_movie']) || !isset($_POST['comment_text'])) {
        return ['error' => 'Parametres manquants.'];
    }

    $idProfile = (int) $_POST['id_profile'];
    $idMovie = (int) $_POST['id_movie'];
    $commentText = trim((string) $_POST['comment_text']);

    if ($idProfile < 1 || $idMovie < 1) {
        return ['error' => 'Identifiants invalides.'];
    }

    if ($commentText === '') {
        return ['error' => 'Le commentaire ne peut pas être vide.'];
    }

    if (mb_strlen($commentText) > 1000) {
        return ['error' => 'Le commentaire est trop long.'];
    }

    $ok = addComment($idProfile, $idMovie, $commentText);
    if ($ok === false) {
        return ['error' => 'Erreur serveur lors de l\'enregistrement du commentaire.'];
    }

    return ['success' => 'Votre commentaire a ete enregistre.'];
}

function readMovieCommentsController()
{
    if (!isset($_REQUEST['id_movie']) || empty($_REQUEST['id_movie'])) {
        return ['error' => 'Parametre id_movie manquant.'];
    }

    $idMovie = (int) $_REQUEST['id_movie'];
    if ($idMovie < 1) {
        return ['error' => 'Identifiant de film invalide.'];
    }

    $comments = getMovieComments($idMovie);
    if ($comments === false) {
        return ['error' => 'Erreur lors du chargement des commentaires.'];
    }

    return $comments;
}

function readPendingMovieCommentsController()
{
    $comments = getPendingMovieComments();
    if ($comments === false) {
        return ['error' => 'Erreur lors du chargement des commentaires en attente.'];
    }

    return $comments;
}

function approveMovieCommentController()
{
    if (!isset($_POST['id_comment'])) {
        return ['error' => 'Parametre id_comment manquant.'];
    }

    $idComment = (int) $_POST['id_comment'];
    if ($idComment < 1) {
        return ['error' => 'Identifiant de commentaire invalide.'];
    }

    $ok = approveMovieComment($idComment);
    if ($ok === false) {
        return ['error' => 'Erreur serveur lors de l\'approbation du commentaire.'];
    }

    return ['success' => 'Le commentaire a ete approuve avec succes.'];
}

function deleteMovieCommentController()
{
    if (!isset($_POST['id_comment'])) {
        return ['error' => 'Parametre id_comment manquant.'];
    }

    $idComment = (int) $_POST['id_comment'];
    if ($idComment < 1) {
        return ['error' => 'Identifiant de commentaire invalide.'];
    }

    $ok = deleteMovieComment($idComment);
    if ($ok === false) {
        return ['error' => 'Erreur serveur lors de la suppression du commentaire.'];
    }

    return ['success' => 'Le commentaire a ete supprime avec succes.'];
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

    // Most active profile (favorites + ratings)
    $active = getMostActiveProfile();
    $stats['most_active_profile'] = $active ? [
        'id' => isset($active['id']) ? intval($active['id']) : null,
        'name' => isset($active['name']) ? $active['name'] : null,
        'favorites' => isset($active['favorites']) ? intval($active['favorites']) : 0,
        'ratings' => isset($active['ratings']) ? intval($active['ratings']) : 0,
        'activity_count' => isset($active['activity_count']) ? intval($active['activity_count']) : 0
    ] : null;

    // Comments counts by status
    $commentsByStatus = getCommentsCountByStatus();
    if ($commentsByStatus === false) {
        $stats['comments_by_status'] = null;
    } else {
        $map = [];
        foreach ($commentsByStatus as $row) {
            $status = $row['comment_status'];
            $map[$status] = intval($row['cnt']);
        }
        $stats['comments_by_status'] = $map;
        $totalComments = array_sum(array_values($map));
        $stats['total_comments'] = $totalComments;
    }

    // Top rated movie
    $topRated = getTopRatedMovie();
    $stats['top_rated_movie'] = $topRated ? [
        'id' => intval($topRated['id']),
        'name' => $topRated['name'],
        'average' => isset($topRated['avg_score']) ? floatval($topRated['avg_score']) : 0,
        'votes' => isset($topRated['vote_count']) ? intval($topRated['vote_count']) : 0
    ] : null;

    // Most recent movie
    $recent = getMostRecentMovie();
    $stats['most_recent_movie'] = $recent ? [
        'id' => intval($recent['id']),
        'name' => $recent['name'],
        'created_at' => $recent['created_at']
    ] : null;

    return $stats;
}