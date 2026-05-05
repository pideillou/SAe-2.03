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

function getNextId($cnx, $table, $column)
{
    $stmt = $cnx->query('SELECT COALESCE(MAX(`' . $column . '`), 0) + 1 AS next_id FROM `' . $table . '`');
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (int) $row['next_id'] : 1;
}

function ensureCommentModerationSchema($cnx)
{
    try {
        $stmt = $cnx->prepare('SELECT COUNT(*) AS cnt
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = :db
              AND TABLE_NAME = :table
              AND COLUMN_NAME = :column');
        $stmt->execute([
            ':db' => DBNAME,
            ':table' => 'MovieComment',
            ':column' => 'comment_status',
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $hasStatusColumn = $row && (int) $row['cnt'] > 0;

        if (!$hasStatusColumn) {
          $cnx->exec("ALTER TABLE `MovieComment`
            ADD COLUMN `comment_status` ENUM('pending', 'approved') NOT NULL DEFAULT 'pending' AFTER `comment_text`");

          $cnx->exec("UPDATE `MovieComment`
            SET `comment_status` = 'approved'
            WHERE `comment_status` IS NULL OR `comment_status` = ''");
        }

        return true;
    } catch (Exception $e) {
        return false;
    }
}

function ensureMovieNewSchema($cnx)
{
    try {
        $stmt = $cnx->prepare('SELECT COUNT(*) AS cnt
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = :db
              AND TABLE_NAME = :table
              AND COLUMN_NAME = :column');

        $stmt->execute([
            ':db' => DBNAME,
            ':table' => 'Movie',
            ':column' => 'created_at',
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $hasCreatedAt = $row && (int) $row['cnt'] > 0;

        $stmt->execute([
            ':db' => DBNAME,
            ':table' => 'Movie',
            ':column' => 'is_featured',
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $hasFeatured = $row && (int) $row['cnt'] > 0;

        if (!$hasFeatured) {
            $cnx->exec("ALTER TABLE `Movie`
                ADD COLUMN `is_featured` TINYINT(1) NOT NULL DEFAULT 0 AFTER `min_age`");
        }

        if (!$hasCreatedAt) {
            $cnx->exec("ALTER TABLE `Movie`
                ADD COLUMN `created_at` TIMESTAMP NULL DEFAULT NULL AFTER `min_age`");

            $cnx->exec("UPDATE `Movie`
                SET `created_at` = DATE_SUB(NOW(), INTERVAL 8 DAY)
                WHERE `created_at` IS NULL");
        }

        return true;
    } catch (Exception $e) {
        return false;
    }
}

function getAllMovies($age = 0)
{
    try {
        $cnx = getConnection();
        if (ensureMovieNewSchema($cnx) === false) {
            return false;
        }
        $sql = "SELECT m.id_movie AS id, m.name, m.image, m.description, m.director, m.year_movie, m.length, m.trailer, m.min_age, m.created_at,
                        CASE WHEN m.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END AS is_new,
                        COALESCE(c.name, 'Sans categorie') AS category
                FROM Movie m
            LEFT JOIN Category c ON m.id_category = c.id_category
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

function searchMovies($query, $age = 0)
{
    try {
        $cnx = getConnection();
        if (ensureMovieNewSchema($cnx) === false) {
            return false;
        }
        $sql = "SELECT m.id_movie AS id, m.name, m.image, m.description, m.director, m.year_movie, m.length, m.trailer, m.min_age, m.is_featured, m.created_at,
                        CASE WHEN m.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END AS is_new,
                        COALESCE(c.name, 'Sans categorie') AS category
                FROM Movie m
            LEFT JOIN Category c ON m.id_category = c.id_category
                WHERE (m.name LIKE :query OR m.director LIKE :query)
                AND m.min_age <= :age
                ORDER BY m.name";
        $stmt = $cnx->prepare($sql);
        $searchQuery = '%' . $query . '%';
        $stmt->bindParam(':query', $searchQuery);
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
        if (ensureMovieNewSchema($cnx) === false) {
            return false;
        }
        $nextId = getNextId($cnx, 'Movie', 'id_movie');
        $sql = 'INSERT INTO Movie (id_movie, name, director, year_movie, length, description, id_category, image, trailer, min_age, created_at, is_featured)
            VALUES (:id_movie, :name, :director, :year_movie, :length, :description, :id_category, :image, :trailer, :min_age, NOW(), 0)';
        $stmt = $cnx->prepare($sql);
        $stmt->bindParam(':id_movie', $nextId, PDO::PARAM_INT);
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
        if (ensureMovieNewSchema($cnx) === false) {
            return false;
        }
        $sql = 'SELECT m.id_movie AS id, m.name, m.image, m.description, m.director, m.year_movie, m.length, m.trailer, m.min_age, m.created_at,
                       CASE WHEN m.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END AS is_new,
                       c.name AS category
                FROM Movie m
            LEFT JOIN Category c ON m.id_category = c.id_category
            WHERE m.id_movie = :id';
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
                $sql = 'INSERT INTO Profile (id_profile, name_profile, image, min_age)
                    VALUES (:id_profile, :name_profile, :image, :min_age)
                    ON DUPLICATE KEY UPDATE
                        name_profile = VALUES(name_profile),
                        image = VALUES(image),
                        min_age = VALUES(min_age)';
            $stmt = $cnx->prepare($sql);
                $stmt->bindParam(':id_profile', $id, PDO::PARAM_INT);
            $stmt->bindParam(':name_profile', $name);
            $stmt->bindParam(':image', $image);
            $stmt->bindParam(':min_age', $minAge, PDO::PARAM_INT);
            $stmt->execute();
            return $id;
        }

            $nextId = getNextId($cnx, 'Profile', 'id_profile');
            $sql = 'INSERT INTO Profile (id_profile, name_profile, image, min_age)
                VALUES (:id_profile, :name_profile, :image, :min_age)';
        $stmt = $cnx->prepare($sql);
            $stmt->bindParam(':id_profile', $nextId, PDO::PARAM_INT);
        $stmt->bindParam(':name_profile', $name);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':min_age', $minAge, PDO::PARAM_INT);
        $stmt->execute();

            return $nextId;
    } catch (Exception $e) {
        return false;
    }
}

function getAllProfiles()
{
    try {
        $cnx = getConnection();
        $sql = 'SELECT id_profile AS id, name_profile AS name, image, min_age FROM Profile ORDER BY name_profile';
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
        $sql = 'INSERT IGNORE INTO Favorite (id_movie_1, id_profile, id_movie, id_profile_1)
            VALUES (:id_movie_1, :id_profile, :id_movie, :id_profile_1)';
        $stmt = $cnx->prepare($sql);
        $stmt->bindParam(':id_movie_1', $idMovie, PDO::PARAM_INT);
        $stmt->bindParam(':id_profile', $idProfile, PDO::PARAM_INT);
        $stmt->bindParam(':id_movie', $idMovie, PDO::PARAM_INT);
        $stmt->bindParam(':id_profile_1', $idProfile, PDO::PARAM_INT);
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
            WHERE id_profile_1 = :id_profile AND id_movie_1 = :id_movie';
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
        $sql = 'SELECT m.id_movie AS id, m.name, m.image, m.description, m.director, m.year_movie, m.length, m.trailer, m.min_age, 
                        COALESCE(c.name, \'Sans categorie\') AS category
                FROM Favorite f
            JOIN Movie m ON f.id_movie_1 = m.id_movie
            LEFT JOIN Category c ON m.id_category = c.id_category
            WHERE f.id_profile_1 = :id_profile
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
        if (ensureMovieNewSchema($cnx) === false) {
            return false;
        }
        $sql = "SELECT m.id_movie AS id, m.name, m.image, m.description, m.director, m.year_movie, m.length, m.trailer, m.min_age, m.created_at, m.is_featured,
                        CASE WHEN m.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END AS is_new,
                        COALESCE(c.name, 'Sans categorie') AS category
                FROM Movie m
            LEFT JOIN Category c ON m.id_category = c.id_category
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

function updateMovieFeatured($idMovie, $isFeatured)
{
    try {
        $cnx = getConnection();
        $sql = 'UPDATE Movie SET is_featured = :is_featured WHERE id_movie = :id_movie';
        $stmt = $cnx->prepare($sql);
        $stmt->bindParam(':is_featured', $isFeatured, PDO::PARAM_INT);
        $stmt->bindParam(':id_movie', $idMovie, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    } catch (Exception $e) {
        return false;
    }
}

function isFavorite($idProfile, $idMovie)
{
    try {
        $cnx = getConnection();
        $sql = 'SELECT 1 FROM Favorite
            WHERE id_profile_1 = :id_profile AND id_movie_1 = :id_movie
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

function isRated($idProfile, $idMovie)
{
    try {
        $cnx = getConnection();
        $sql = 'SELECT 1 FROM Rating
            WHERE id_profile_1 = :id_profile AND id_movie = :id_movie
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

function addRating($idProfile, $idMovie, $score)
{
    try {
        $cnx = getConnection();

        // Prevent duplicate rating
        $already = isRated($idProfile, $idMovie);
        if ($already === true) {
            return 0;
        }

        $nextId = getNextId($cnx, 'Rating', 'id_rating');
        $sql = 'INSERT INTO Rating (id_rating, id_profile, id_movie, score, created_at, id_profile_1)
            VALUES (:id_rating, :id_profile, :id_movie, :score, NOW(), :id_profile_1)
            ON DUPLICATE KEY UPDATE
                id_profile = VALUES(id_profile),
                id_movie = VALUES(id_movie),
                score = VALUES(score),
                created_at = VALUES(created_at),
                id_profile_1 = VALUES(id_profile_1)';
        $stmt = $cnx->prepare($sql);
        $stmt->bindParam(':id_rating', $nextId, PDO::PARAM_INT);
        $stmt->bindParam(':id_profile', $idProfile, PDO::PARAM_INT);
        $stmt->bindParam(':id_movie', $idMovie, PDO::PARAM_INT);
        $stmt->bindParam(':score', $score, PDO::PARAM_INT);
        $stmt->bindParam(':id_profile_1', $idProfile, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    } catch (Exception $e) {
        return false;
    }
}

function getMovieAverageRating($idMovie)
{
    try {
        $cnx = getConnection();
        $sql = 'SELECT COUNT(*) AS cnt, COALESCE(ROUND(AVG(score),2), 0) AS avg_score
                FROM Rating
                WHERE id_movie = :id_movie';
        $stmt = $cnx->prepare($sql);
        $stmt->bindParam(':id_movie', $idMovie, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return false;
    }
}

function getUserRating($idProfile, $idMovie)
{
    try {
        $cnx = getConnection();
        $sql = 'SELECT score FROM Rating
            WHERE id_profile_1 = :id_profile AND id_movie = :id_movie
                LIMIT 1';
        $stmt = $cnx->prepare($sql);
        $stmt->bindParam(':id_profile', $idProfile, PDO::PARAM_INT);
        $stmt->bindParam(':id_movie', $idMovie, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['score'] : null;
    } catch (Exception $e) {
        return false;
    }
}

function addComment($idProfile, $idMovie, $commentText)
{
    try {
        $cnx = getConnection();
        if (ensureCommentModerationSchema($cnx) === false) {
            return false;
        }
        $nextId = getNextId($cnx, 'MovieComment', 'id_comment');
        $sql = 'INSERT INTO MovieComment (id_comment, id_profile, id_movie, comment_text, comment_status, created_at, id_profile_1)
            VALUES (:id_comment, :id_profile, :id_movie, :comment_text, :comment_status, NOW(), :id_profile_1)';
        $stmt = $cnx->prepare($sql);
        $stmt->bindParam(':id_comment', $nextId, PDO::PARAM_INT);
        $stmt->bindParam(':id_profile', $idProfile, PDO::PARAM_INT);
        $stmt->bindParam(':id_movie', $idMovie, PDO::PARAM_INT);
        $stmt->bindParam(':comment_text', $commentText);
        $status = 'pending';
        $stmt->bindParam(':comment_status', $status);
        $stmt->bindParam(':id_profile_1', $idProfile, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    } catch (Exception $e) {
        return false;
    }
}

function getMovieComments($idMovie)
{
    try {
        $cnx = getConnection();
        if (ensureCommentModerationSchema($cnx) === false) {
            return false;
        }
        $sql = 'SELECT mc.id_comment AS id, mc.comment_text, mc.comment_status, mc.created_at, p.name_profile AS profile_name
                FROM MovieComment mc
            JOIN Profile p ON mc.id_profile_1 = p.id_profile
                WHERE mc.id_movie = :id_movie AND mc.comment_status = "approved"
            ORDER BY mc.created_at DESC, mc.id_comment DESC';
        $stmt = $cnx->prepare($sql);
        $stmt->bindParam(':id_movie', $idMovie, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return false;
    }
}

function getPendingMovieComments()
{
    try {
        $cnx = getConnection();
        if (ensureCommentModerationSchema($cnx) === false) {
            return false;
        }
        $sql = 'SELECT mc.id_comment AS id, mc.comment_text, mc.comment_status, mc.created_at, mc.id_movie, m.name AS movie_name, p.name_profile AS profile_name
                FROM MovieComment mc
            JOIN Profile p ON mc.id_profile_1 = p.id_profile
            JOIN Movie m ON mc.id_movie = m.id_movie
                WHERE mc.comment_status = "pending"
            ORDER BY mc.created_at ASC, mc.id_comment ASC';
        $stmt = $cnx->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return false;
    }
}

function approveMovieComment($idComment)
{
    try {
        $cnx = getConnection();
        if (ensureCommentModerationSchema($cnx) === false) {
            return false;
        }
        $sql = 'UPDATE MovieComment SET comment_status = "approved" WHERE id_comment = :id_comment';
        $stmt = $cnx->prepare($sql);
        $stmt->bindParam(':id_comment', $idComment, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    } catch (Exception $e) {
        return false;
    }
}

function deleteMovieComment($idComment)
{
    try {
        $cnx = getConnection();
        if (ensureCommentModerationSchema($cnx) === false) {
            return false;
        }
        $sql = 'DELETE FROM MovieComment WHERE id_comment = :id_comment';
        $stmt = $cnx->prepare($sql);
        $stmt->bindParam(':id_comment', $idComment, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
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
                    GROUP BY id_profile_1
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
        $sql = 'SELECT m.id_movie AS id, m.name, COUNT(*) AS favorite_count
                FROM Favorite f
            JOIN Movie m ON f.id_movie_1 = m.id_movie
            GROUP BY f.id_movie_1, m.id_movie, m.name
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
            JOIN Movie m ON f.id_movie_1 = m.id_movie
            LEFT JOIN Category c ON m.id_category = c.id_category
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

function getMostActiveProfile()
{
    try {
        $cnx = getConnection();
        $sql = 'SELECT p.id_profile AS id, p.name_profile AS name,
                       COALESCE(fav.fav_cnt,0) AS favorites,
                       COALESCE(r.rate_cnt,0) AS ratings,
                       (COALESCE(fav.fav_cnt,0) + COALESCE(r.rate_cnt,0)) AS activity_count
                FROM Profile p
            LEFT JOIN (SELECT id_profile_1, COUNT(*) AS fav_cnt FROM Favorite GROUP BY id_profile_1) fav ON p.id_profile = fav.id_profile_1
            LEFT JOIN (SELECT id_profile_1, COUNT(*) AS rate_cnt FROM Rating GROUP BY id_profile_1) r ON p.id_profile = r.id_profile_1
                ORDER BY activity_count DESC
                LIMIT 1';
        $stmt = $cnx->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return false;
    }
}

function getCommentsCountByStatus()
{
    try {
        $cnx = getConnection();
        if (ensureCommentModerationSchema($cnx) === false) {
            return false;
        }
        $sql = 'SELECT comment_status, COUNT(*) AS cnt FROM MovieComment GROUP BY comment_status';
        $stmt = $cnx->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return false;
    }
}

function getTopRatedMovie()
{
    try {
        $cnx = getConnection();
        $sql = 'SELECT m.id_movie AS id, m.name, COALESCE(ROUND(AVG(r.score),2),0) AS avg_score, COUNT(r.id_rating) AS vote_count
                FROM Rating r
            JOIN Movie m ON r.id_movie = m.id_movie
            GROUP BY m.id_movie, m.name
                ORDER BY avg_score DESC, vote_count DESC
                LIMIT 1';
        $stmt = $cnx->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return false;
    }
}

function getMostRecentMovie()
{
    try {
        $cnx = getConnection();
        if (ensureMovieNewSchema($cnx) === false) {
            return false;
        }
        $sql = 'SELECT id_movie AS id, name, created_at FROM Movie ORDER BY created_at DESC LIMIT 1';
        $stmt = $cnx->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return false;
    }
}