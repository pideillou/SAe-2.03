<?php

/** ARCHITECTURE PHP SERVEUR  : Rôle du fichier controller.php
 * 
 *  Dans ce fichier, on va définir les fonctions de contrôle qui vont traiter les requêtes HTTP.
 *  Les requêtes HTTP sont interprétées selon la valeur du paramètre 'todo' de la requête (voir script.php)
 *  Pour chaque valeur différente, on déclarera une fonction de contrôle différente.
 * 
 *  Les fonctions de contrôle vont éventuellement lire les paramètres additionnels de la requête, 
 *  les vérifier, puis appeler les fonctions du modèle (model.php) pour effectuer les opérations
 *  nécessaires sur la base de données.
 *  
 *  Si la fonction échoue à traiter la requête, elle retourne false (mauvais paramètres, erreur de connexion à la BDD, etc.)
 *  Sinon elle retourne le résultat de l'opération (des données ou un message) à includre dans la réponse HTTP.
 */

/** Inclusion du fichier model.php
 *  Pour pouvoir utiliser les fonctions qui y sont déclarées et qui permettent
 *  de faire des opérations sur les données stockées en base de données.
 */
require("model.php");


function readMoviesController(){
    $movies = getAllMovies();
    return $movies;
}

function addMovieController() {
    // Vérifie que tous les champs attendus sont présents
    $required = ['name', 'director', 'year', 'length', 'description', 'id_category', 'image', 'trailer', 'min_age'];
    foreach ($required as $field) {
        if (!isset($_POST[$field])) {
            return ["error" => "Champ manquant : $field"];
        }
    }
    // Appelle la fonction du modèle
    $result = addMovie(
        $_POST['name'],
        $_POST['director'],
        $_POST['year'],
        $_POST['length'],
        $_POST['description'],
        $_POST['id_category'],
        $_POST['image'],
        $_POST['trailer'],
        $_POST['min_age']
    );
    if ($result === false) {
        return ["error" => "Erreur lors de l'ajout du film."];
    }
    return ["success" => true];
}

function readMovieDetailController() {
    if (!isset($_GET['id'])) return false;
    $id = intval($_GET['id']);
    return getMovieDetail($id);
}

function addProfileController() {
    // Vérifie que tous les champs attendus sont présents
    $required = ['name', 'min_age'];
    foreach ($required as $field) {
        if (!isset($_POST[$field]) || $_POST[$field] === '') {
            return ["error" => "Champ manquant : $field"];
        }
    }
    
    // Valide que le nom n'est pas vide
    if (strlen(trim($_POST['name'])) === 0) {
        return ["error" => "Le nom du profil ne peut pas être vide."];
    }
    
    // Valide que min_age est un nombre entier valide
    $min_age = intval($_POST['min_age']);
    if ($min_age < 0 || $min_age > 18) {
        return ["error" => "L'âge minimum doit être entre 0 et 18."];
    }
    
    // Image est optionnelle
    $image = isset($_POST['image']) ? $_POST['image'] : null;
    
    // Appelle la fonction du modèle
    $result = addProfile(
        trim($_POST['name']),
        $image,
        $min_age
    );
    
    if ($result === false) {
        return ["error" => "Erreur lors de l'ajout du profil."];
    }
    
    return ["success" => "Le profil a été ajouté avec succès."];
}

function readProfilesController() {
    $profiles = getAllProfiles();
    if ($profiles === false) {
        return ["error" => "Erreur lors de la récupération des profils."];
    }
    return $profiles;
}