<?php
/**
 * api_add_wishlist.php - API AJAX pour ajouter un livre à la liste
 * Retourne du JSON
 */
header('Content-Type: application/json');
require_once 'config.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Méthode non autorisée';
    echo json_encode($response);
    exit;
}

$id_livre = isset($_POST['id_livre']) ? intval($_POST['id_livre']) : 0;
$id_lecteur = isset($_POST['id_lecteur']) ? intval($_POST['id_lecteur']) : 1;

if ($id_livre <= 0) {
    $response['message'] = 'ID livre invalide';
    echo json_encode($response);
    exit;
}

$db = getDBConnection();
if (!$db) {
    $response['message'] = 'Erreur de connexion';
    echo json_encode($response);
    exit;
}

try {
    // Vérifier disponibilité
    $stmt = $db->prepare("SELECT nombre_exemplaire FROM livres WHERE id = :id");
    $stmt->execute([':id' => $id_livre]);
    $book = $stmt->fetch();

    if (!$book || $book['nombre_exemplaire'] <= 0) {
        $response['message'] = 'Livre non disponible';
        echo json_encode($response);
        exit;
    }

    // Vérifier doublon
    $stmt = $db->prepare("SELECT 1 FROM liste_lecture WHERE id_livre = :id_livre AND id_lecteur = :id_lecteur");
    $stmt->execute([':id_livre' => $id_livre, ':id_lecteur' => $id_lecteur]);

    if ($stmt->fetch()) {
        $response['message'] = 'Déjà dans votre liste';
        echo json_encode($response);
        exit;
    }

    // Ajouter
    $stmt = $db->prepare("
        INSERT INTO liste_lecture (id_livre, id_lecteur, date_emprunt, date_retour)
        VALUES (:id_livre, :id_lecteur, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY))
    ");
    $stmt->execute([':id_livre' => $id_livre, ':id_lecteur' => $id_lecteur]);

    // Décrémenter
    $stmt = $db->prepare("UPDATE livres SET nombre_exemplaire = nombre_exemplaire - 1 WHERE id = :id");
    $stmt->execute([':id' => $id_livre]);

    $response['success'] = true;
    $response['message'] = 'Ajouté avec succès';

} catch (PDOException $e) {
    $response['message'] = 'Erreur serveur';
}

echo json_encode($response);
?>