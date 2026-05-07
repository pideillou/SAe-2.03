<?php
/**
 * DEBUG SCRIPT - SAe-2.03
 * Script de diagnostic complet pour identifier les erreurs du site
 * 
 * Utilisation : Accédez à https://pideill-sae203.mmi-limoges.fr/server/debug.php
 * Puis supprimez ce fichier après avoir collecté les informations
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Affichage des erreurs en HTML lisible
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug SAe-2.03</title>
    <style>
        body { font-family: monospace; margin: 20px; background: #f5f5f5; }
        .section { background: white; margin: 20px 0; padding: 15px; border-left: 4px solid #007bff; }
        .section h2 { margin-top: 0; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        .info { color: blue; }
        pre { background: #f0f0f0; padding: 10px; overflow-x: auto; border-radius: 3px; }
        code { background: #f0f0f0; padding: 2px 5px; border-radius: 2px; }
        .file-status { margin: 10px 0; padding: 10px; background: #f9f9f9; border: 1px solid #ddd; }
    </style>
</head>
<body>

<h1>🔧 Diagnostic SAe-2.03</h1>

<?php

// ================== 1. INFO SERVEUR ==================
echo '<div class="section">';
echo '<h2>ℹ️ Environnement Serveur</h2>';
echo '<table border="1" cellpadding="10">';
echo '<tr><td>PHP Version</td><td>' . phpversion() . '</td></tr>';
echo '<tr><td>System</td><td>' . php_uname() . '</td></tr>';
echo '<tr><td>Répertoire courant</td><td>' . getcwd() . '</td></tr>';
echo '<tr><td>OS User</td><td>' . get_current_user() . '</td></tr>';
echo '</table>';
echo '</div>';

// ================== 2. FICHIERS REQUIS ==================
echo '<div class="section">';
echo '<h2>📁 Vérification des fichiers</h2>';

$required_files = [
    '../server/model.php' => 'Modèle (accès BD)',
    '../server/controller.php' => 'Contrôleur (logique métier)',
    '../server/script.php' => 'Router (point d\'entrée)',
    '../app/index.html' => 'Frontend app',
    '../admin/index.html' => 'Frontend admin',
];

foreach ($required_files as $file => $desc) {
    $full_path = __DIR__ . '/' . $file;
    $exists = file_exists($full_path);
    $readable = $exists ? is_readable($full_path) : false;
    
    echo '<div class="file-status">';
    echo '<strong>' . basename($file) . '</strong> (' . $desc . ')<br>';
    echo 'Chemin: <code>' . $full_path . '</code><br>';
    echo 'Existe: ' . ($exists ? '<span class="success">✓ OUI</span>' : '<span class="error">✗ NON</span>') . '<br>';
    echo 'Lisible: ' . ($readable ? '<span class="success">✓ OUI</span>' : '<span class="warning">⚠ NON</span>') . '';
    echo '</div>';
}

echo '</div>';

// ================== 3. TEST CONNEXION BD ==================
echo '<div class="section">';
echo '<h2>🗄️ Connexion à la Base de Données</h2>';

require_once(__DIR__ . '/model.php');

echo 'Configuration définie:<br>';
echo '<code>';
echo 'HOST: ' . HOST . '<br>';
echo 'DBNAME: ' . DBNAME . '<br>';
echo 'DBLOGIN: ' . DBLOGIN . '<br>';
echo 'DBPWD: ' . (strlen(DBPWD) > 0 ? str_repeat('*', strlen(DBPWD)) : '(vide)') . '<br>';
echo '</code><br>';

try {
    $cnx = getConnection();
    echo '<span class="success">✓ Connexion établie</span><br>';
    
    // Test de requête simple
    try {
        $stmt = $cnx->prepare("SELECT COUNT(*) as count FROM Movie LIMIT 1");
        $stmt->execute();
        $result = $stmt->fetch();
        echo '<span class="success">✓ Requête SELECT fonctionnelle</span><br>';
        echo 'Nombre de films: ' . $result->count . '<br>';
    } catch (Exception $e) {
        echo '<span class="error">✗ Erreur SELECT: ' . $e->getMessage() . '</span><br>';
    }
    
} catch (Throwable $e) {
    echo '<span class="error">✗ Connexion échouée</span><br>';
    echo 'Erreur: ' . get_class($e) . ': ' . $e->getMessage() . '<br>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}

echo '</div>';

// ================== 4. TEST FONCTIONS MODÈLE ==================
echo '<div class="section">';
echo '<h2>🔌 Test des fonctions métier</h2>';

$functions_to_test = [
    'getAllMovies(0)' => 'getAllMovies',
    'getAllProfiles()' => 'getAllProfiles',
    'getTotalMovies()' => 'getTotalMovies',
    'getTotalProfiles()' => 'getTotalProfiles',
];

foreach ($functions_to_test as $label => $func) {
    echo '<div style="margin: 10px 0; padding: 10px; background: #f9f9f9; border: 1px solid #ddd;">';
    echo '<strong>' . $label . '</strong><br>';
    
    try {
        $result = call_user_func($func);
        if ($result === false) {
            echo '<span class="error">✗ Fonction a retourné false</span>';
        } else {
            if (is_array($result)) {
                echo '<span class="success">✓ OK</span> - Résultat: ';
                if (count($result) > 0) {
                    echo count($result) . ' élément(s)';
                    if (is_object($result[0])) {
                        echo ' (objets)';
                    }
                } else {
                    echo '(tableau vide)';
                }
            } else {
                echo '<span class="success">✓ OK</span> - Résultat: ';
                echo is_array($result) ? json_encode($result) : var_export($result, true);
            }
        }
    } catch (Exception $e) {
        echo '<span class="error">✗ Exception: ' . $e->getMessage() . '</span>';
    }
    
    echo '</div>';
}

echo '</div>';

// ================== 5. TEST ENDPOINTS ==================
echo '<div class="section">';
echo '<h2>🌐 Test des endpoints API</h2>';

require_once(__DIR__ . '/controller.php');

$endpoints = [
    ['todo' => 'readProfiles', 'label' => 'Lire les profils'],
    ['todo' => 'readMovies', 'label' => 'Lire les films', 'params' => ['age' => 0]],
    ['todo' => 'getStatistics', 'label' => 'Statistiques'],
];

foreach ($endpoints as $ep) {
    echo '<div style="margin: 10px 0; padding: 10px; background: #f9f9f9; border: 1px solid #ddd;">';
    echo '<strong>' . $ep['label'] . '</strong> (todo=' . $ep['todo'] . ')<br>';
    
    // Simuler la requête
    $_REQUEST = array_merge(['todo' => $ep['todo']], $ep['params'] ?? []);
    
    try {
        switch ($ep['todo']) {
            case 'readProfiles':
                $data = readProfilesController();
                break;
            case 'readMovies':
                $data = readMoviesController($ep['params']['age'] ?? 0);
                break;
            case 'getStatistics':
                $data = getStatisticsController();
                break;
            default:
                $data = null;
        }
        
        if ($data === false) {
            echo '<span class="error">✗ Contrôleur a retourné false</span>';
        } else {
            echo '<span class="success">✓ OK</span><br>';
            echo '<pre>' . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
        }
    } catch (Exception $e) {
        echo '<span class="error">✗ Exception: ' . $e->getMessage() . '</span><br>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    }
    
    echo '</div>';
}

echo '</div>';

// ================== 6. LOGS ERREURS ==================
echo '<div class="section">';
echo '<h2>📋 Logs d\'erreurs PHP</h2>';

$log_file = '/tmp/php_errors.log';
if (file_exists($log_file)) {
    $lines = file($log_file);
    $recent = array_slice($lines, -20);
    echo '<pre>';
    foreach ($recent as $line) {
        echo htmlspecialchars($line);
    }
    echo '</pre>';
} else {
    echo '<span class="info">Aucun fichier de log d\'erreurs trouvé ou logs vides</span>';
}

echo '</div>';

// ================== 7. RECOMMANDATIONS ==================
echo '<div class="section" style="background: #fff3cd; border-left-color: #ffc107;">';
echo '<h2>✅ Prochaines étapes</h2>';
echo '<ul>';
echo '<li><strong>Si la BD refuse la connexion:</strong> Corrigez HOST, DBNAME, DBLOGIN, DBPWD dans <code>server/model.php</code></li>';
echo '<li><strong>Si les fichiers sont manquants:</strong> Vérifiez la structure du projet sur le serveur</li>';
echo '<li><strong>Si tout fonctionne:</strong> Le problème vient du client JavaScript ou des chemins d\'URL</li>';
echo '<li><strong>Après diagnostic:</strong> Supprimez ce fichier debug.php du serveur (sécurité)</li>';
echo '</ul>';
echo '</div>';

?>

</body>
</html>
