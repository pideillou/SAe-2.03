<?php

ini_set('display_errors', 0);
error_reporting(E_ALL);

require('controller.php');

function sendJson($payload, $statusCode)
{
    http_response_code($statusCode);
    header('Content-Type: application/json');

    $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        http_response_code(500);
        echo json_encode(['error' => 'JSON encoding error'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    echo $json;
    exit();
}

if (isset($_REQUEST['todo'])) {
    $todo = $_REQUEST['todo'];

    switch ($todo) {
        case 'readMovies':
            $age = isset($_REQUEST['age']) ? (int) $_REQUEST['age'] : 0;
            $data = readMoviesController($age);
            break;

        case 'addMovie':
            $data = addMovieController();
            break;

        case 'readMovieDetail':
            $data = readMovieDetailController();
            break;

        case 'searchMovies':
            $data = searchMoviesController();
            break;

        case 'readFeaturedMovies':
            $data = readFeaturedMoviesController();
            break;

        case 'updateMovieFeatured':
            $data = updateMovieFeaturedController();
            break;

        case 'addRating':
            $data = addRatingController();
            break;

        case 'getMovieRating':
            $data = getMovieRatingController();
            break;

        case 'addComment':
            $data = addCommentController();
            break;

        case 'readMovieComments':
            $data = readMovieCommentsController();
            break;

        case 'readPendingMovieComments':
            $data = readPendingMovieCommentsController();
            break;

        case 'approveMovieComment':
            $data = approveMovieCommentController();
            break;

        case 'deleteMovieComment':
            $data = deleteMovieCommentController();
            break;

        case 'addProfile':
            $data = addProfileController();
            break;

        case 'saveProfile':
            $data = saveProfileController();
            break;

        case 'readProfiles':
            $data = readProfilesController();
            break;

        case 'addFavorite':
            $data = addFavoriteController();
            break;

        case 'removeFavorite':
            $data = removeFavoriteController();
            break;

        case 'readFavorites':
            $data = readFavoritesController();
            break;

        case 'getStatistics':
            $data = getStatisticsController();
            break;
    }

    if ($data === false) {
        sendJson(['error' => 'Controller returns false'], 500);
    }

    sendJson($data, 200);
}

http_response_code(404);

?>