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

function getAllMovies($age = 0)
{
    try {
        $cnx = getConnection();
        $sql = "SELECT m.id, m.name, m.image, m.description, m.director, m.year_movie, m.length, m.trailer, m.min_age, COALESCE(c.name, 'Sans categorie') AS category
                FROM Movie m
                LEFT JOIN Category c ON m.id_category = c.id
        WHERE m.min_age <= :age
                ORDER BY category, m.name";
        $stmt = $cnx->prepare($sql);
    $stmt->bindParam(':age', $age, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return false;
    }
}

function addMovie($name, $director, $year_movie, $length, $description, $idCategory, $image, $trailer, $minAge)
{
    try {
        $cnx = getConnection();
        $sql = 'INSERT INTO Movie (name, director, year_movie, length, description, id_category, image, trailer, min_age)
                VALUES (:name, :director, :year_movie, :length, :description, :id_category, :image, :trailer, :min_age)';
        $stmt = $cnx->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':director', $director);
        $stmt->bindParam(':year_movie', $year_movie, PDO::PARAM_INT);
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
        $sql = 'SELECT m.id, m.name, m.image, m.description, m.director, m.year_movie, m.length, m.trailer, m.min_age, c.name AS category
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
    return saveProfile(0, $name, $image, $minAge);
}

function saveProfile($id, $name, $image, $minAge)
{
    try {
        $cnx = getConnection();

        if ($id > 0) {
            $sql = 'INSERT INTO Profile (id, name_profile, image, min_age)
                    VALUES (:id, :name_profile, :image, :min_age)
                    ON DUPLICATE KEY UPDATE
                        name_profile = VALUES(name_profile),
                        image = VALUES(image),
                        min_age = VALUES(min_age)';
            $stmt = $cnx->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':name_profile', $name);
            $stmt->bindParam(':image', $image);
            $stmt->bindParam(':min_age', $minAge, PDO::PARAM_INT);
            $stmt->execute();
            return $id;
        }

        $sql = 'INSERT INTO Profile (name_profile, image, min_age)
                VALUES (:name_profile, :image, :min_age)';
        $stmt = $cnx->prepare($sql);
        $stmt->bindParam(':name_profile', $name);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':min_age', $minAge, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $cnx->lastInsertId();
    } catch (Exception $e) {
        return false;
    }
}

function getAllProfiles()
{
    try {
        $cnx = getConnection();
        $sql = 'SELECT id, name_profile AS name, image, min_age FROM Profile ORDER BY name_profile';
        $stmt = $cnx->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return false;
    }
}

function addFavorite($idProfile, $idMovie)
{
    try {
        $cnx = getConnection();
        $sql = 'INSERT IGNORE INTO Favorite (id_profile, id_movie)
                VALUES (:id_profile, :id_movie)';
        $stmt = $cnx->prepare($sql);
        $stmt->bindParam(':id_profile', $idProfile, PDO::PARAM_INT);
        $stmt->bindParam(':id_movie', $idMovie, PDO::PARAM_INT);
        $stmt->execute();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function removeFavorite($idProfile, $idMovie)
{
    try {
        $cnx = getConnection();
        $sql = 'DELETE FROM Favorite
                WHERE id_profile = :id_profile AND id_movie = :id_movie';
        $stmt = $cnx->prepare($sql);
        $stmt->bindParam(':id_profile', $idProfile, PDO::PARAM_INT);
        $stmt->bindParam(':id_movie', $idMovie, PDO::PARAM_INT);
        $stmt->execute();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function getFavorites($idProfile)
{
    try {
        $cnx = getConnection();
        $sql = 'SELECT m.id, m.name, m.image, m.description, m.director, m.year_movie, m.length, m.trailer, m.min_age, 
                        COALESCE(c.name, \'Sans categorie\') AS category
                FROM Favorite f
                JOIN Movie m ON f.id_movie = m.id
                LEFT JOIN Category c ON m.id_category = c.id
                WHERE f.id_profile = :id_profile
                ORDER BY m.name';
        $stmt = $cnx->prepare($sql);
        $stmt->bindParam(':id_profile', $idProfile, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return false;
    }
}

function getFeaturedMovies($age = 0)
{
    try {
        $cnx = getConnection();
        $sql = "SELECT m.id, m.name, m.image, m.description, m.director, m.year_movie, m.length, m.trailer, m.min_age, 
                        COALESCE(c.name, 'Sans categorie') AS category
                FROM Movie m
                LEFT JOIN Category c ON m.id_category = c.id
                WHERE m.is_featured = 1 AND m.min_age <= :age
                ORDER BY m.name";
        $stmt = $cnx->prepare($sql);
        $stmt->bindParam(':age', $age, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return false;
    }
}

function isFavorite($idProfile, $idMovie)
{
    try {
        $cnx = getConnection();
        $sql = 'SELECT 1 FROM Favorite
                WHERE id_profile = :id_profile AND id_movie = :id_movie
                LIMIT 1';
        $stmt = $cnx->prepare($sql);
        $stmt->bindParam(':id_profile', $idProfile, PDO::PARAM_INT);
        $stmt->bindParam(':id_movie', $idMovie, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch() !== false;
    } catch (Exception $e) {
        return false;
    }
}

// Statistics functions
function getTotalProfiles()
{
    try {
        $cnx = getConnection();
        $sql = 'SELECT COUNT(*) AS total_profiles FROM Profile';
        $stmt = $cnx->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return false;
    }
}

function getTotalMovies()
{
    try {
        $cnx = getConnection();
        $sql = 'SELECT COUNT(*) AS total_movies FROM Movie';
        $stmt = $cnx->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return false;
    }
}

function getAverageFavoritesPerProfile()
{
    try {
        $cnx = getConnection();
        $sql = 'SELECT 
                    COALESCE(ROUND(AVG(favorite_count), 2), 0) AS avg_favorites
                FROM (
                    SELECT COUNT(*) AS favorite_count
                    FROM Favorite
                    GROUP BY id_profile
                ) AS profile_favorites';
        $stmt = $cnx->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return false;
    }
}

function getMostFavoritedMovie()
{
    try {
        $cnx = getConnection();
        $sql = 'SELECT m.id, m.name, COUNT(*) AS favorite_count
                FROM Favorite f
                JOIN Movie m ON f.id_movie = m.id
                GROUP BY f.id_movie, m.id, m.name
                ORDER BY favorite_count DESC
                LIMIT 1';
        $stmt = $cnx->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return false;
    }
}

function getMostPopularCategory()
{
    try {
        $cnx = getConnection();
        $sql = 'SELECT c.name, COUNT(*) AS favorite_count
                FROM Favorite f
                JOIN Movie m ON f.id_movie = m.id
                LEFT JOIN Category c ON m.id_category = c.id
                GROUP BY m.id_category, c.name
                ORDER BY favorite_count DESC
                LIMIT 1';
        $stmt = $cnx->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return false;
    }
}