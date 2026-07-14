<?php
/**
 * admin_delete.php - Suppression d'un livre
 */
require_once 'auth.php';
require_once 'config.php';

requireLogin();

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php?error=' . urlencode('Méthode non autorisée'));
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id <= 0) {
    header('Location: admin.php?error=' . urlencode('ID de livre invalide'));
    exit;
}

$db = getDBConnection();
if (!$db) {
    header('Location: admin.php?error=' . urlencode('Erreur de connexion à la base de données'));
    exit;
}

try {
    // Vérifier que le livre existe
    $stmt = $db->prepare("SELECT id FROM livres WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $book = $stmt->fetch();

    if (!$book) {
        header('Location: admin.php?error=' . urlencode('Livre introuvable'));
        exit;
    }

    // Supprimer le livre (les relations dans liste_lecture seront supprimées automatiquement grâce à ON DELETE CASCADE)
    $stmt = $db->prepare("DELETE FROM livres WHERE id = :id");
    $stmt->execute([':id' => $id]);

    header('Location: admin.php?success=' . urlencode('Livre supprimé avec succès'));
    exit;
} catch (PDOException $e) {
    error_log("Erreur suppression livre: " . $e->getMessage());
    header('Location: admin.php?error=' . urlencode('Erreur lors de la suppression du livre'));
    exit;
}
?>
