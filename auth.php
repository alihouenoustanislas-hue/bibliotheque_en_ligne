<?php
/**
 * auth.php - Gestion de l'authentification et des sessions
 */

// Démarrer la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

/**
 * Vérifie si un utilisateur est connecté
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']);
}

/**
 * Authentifie un utilisateur
 * @param string $username
 * @param string $password
 * @return array ['success' => bool, 'message' => string]
 */
function login($username, $password) {
    $db = getDBConnection();
    if (!$db) {
        return ['success' => false, 'message' => 'Erreur de connexion à la base de données'];
    }

    try {
        $stmt = $db->prepare("SELECT * FROM administrateurs WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            // Authentification réussie
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_nom'] = $admin['nom'];
            $_SESSION['admin_email'] = $admin['email'];
            return ['success' => true, 'message' => 'Connexion réussie'];
        } else {
            return ['success' => false, 'message' => 'Nom d\'utilisateur ou mot de passe incorrect'];
        }
    } catch (PDOException $e) {
        error_log("Erreur login: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erreur lors de la connexion'];
    }
}

/**
 * Déconnecte l'utilisateur
 */
function logout() {
    session_unset();
    session_destroy();
}

/**
 * Redirige vers la page de connexion si non authentifié
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

/**
 * Obtient les informations de l'utilisateur connecté
 * @return array|null
 */
function getCurrentAdmin() {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id' => $_SESSION['admin_id'],
        'username' => $_SESSION['admin_username'],
        'nom' => $_SESSION['admin_nom'],
        'email' => $_SESSION['admin_email']
    ];
}
?>
