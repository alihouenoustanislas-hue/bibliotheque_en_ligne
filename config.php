<?php
/**
 * Configuration de la base de données
 * A MODIFIER selon votre environnement local (XAMPP, WAMP, MAMP, etc.)
 */

// Paramètres de connexion MySQL
// Par défaut pour XAMPP : serveur=localhost, utilisateur=root, mot de passe vide
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // <- Modifier si nécessaire
define('DB_PASS', '');           // <- Modifier si nécessaire
define('DB_NAME', 'bibliotheque');

/**
 * Établit une connexion PDO à la base de données
 * @return PDO|null
 */
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // En production, ne pas afficher l'erreur directement
        error_log("Erreur de connexion : " . $e->getMessage());
        return null;
    }
}

/**
 * Vérifie si la connexion est active
 * @return bool
 */
function isDBConnected() {
    $db = getDBConnection();
    return ($db !== null);
}
?>