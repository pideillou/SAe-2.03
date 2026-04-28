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
            $data = readMoviesController();
            break;

        case 'addMovie':
            $data = addMovieController();
            break;

        case 'readMovieDetail':
            $data = readMovieDetailController();
            break;

        case 'addProfile':
            $data = addProfileController();
            break;

        case 'readProfiles':
            $data = readProfilesController();
            break;

        default:
            sendJson(['error' => 'Unknown todo value'], 400);
    }

    if ($data === false) {
        sendJson(['error' => 'Controller returns false'], 500);
    }

    sendJson($data, 200);
}

http_response_code(404);

?>