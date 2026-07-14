<?php
/**
 * wishlist_add.php - Ajoute un livre à la liste de lecture d'un lecteur
 */
require_once 'config.php';

$id_livre = isset($_GET['id']) ? intval($_GET['id']) : 0;
// Dans un vrai système, l'ID lecteur viendrait de la session
$id_lecteur = isset($_GET['lecteur']) ? intval($_GET['lecteur']) : 1;

if ($id_livre <= 0) {
    header('Location: results.php?msg=Livre+non+valide&type=error');
    exit;
}

$db = getDBConnection();
if (!$db) {
    header('Location: results.php?msg=Erreur+de+connexion+base+de+données&type=error');
    exit;
}

try {
    // Vérifier si le livre existe et a des exemplaires disponibles
    $stmt = $db->prepare("SELECT nombre_exemplaire FROM livres WHERE id = :id");
    $stmt->execute([':id' => $id_livre]);
    $book = $stmt->fetch();

    if (!$book) {
        header('Location: results.php?msg=Livre+introuvable&type=error');
        exit;
    }

    if ($book['nombre_exemplaire'] <= 0) {
        header('Location: details.php?id=' . $id_livre . '&msg=Ce+livre+n\'est+plus+disponible&type=error');
        exit;
    }

    // Vérifier si le livre est déjà dans la liste
    $stmt = $db->prepare("SELECT 1 FROM liste_lecture WHERE id_livre = :id_livre AND id_lecteur = :id_lecteur");
    $stmt->execute([':id_livre' => $id_livre, ':id_lecteur' => $id_lecteur]);

    if ($stmt->fetch()) {
        header('Location: wishlist.php?msg=Ce+livre+est+déjà+dans+votre+liste&type=info');
        exit;
    }

    // Ajouter à la liste de lecture
    $date_emprunt = date('Y-m-d');
    $date_retour = date('Y-m-d', strtotime('+30 days'));

    $stmt = $db->prepare("
        INSERT INTO liste_lecture (id_livre, id_lecteur, date_emprunt, date_retour)
        VALUES (:id_livre, :id_lecteur, :date_emprunt, :date_retour)
    ");
    $stmt->execute([
        ':id_livre' => $id_livre,
        ':id_lecteur' => $id_lecteur,
        ':date_emprunt' => $date_emprunt,
        ':date_retour' => $date_retour
    ]);

    // Décrémenter le nombre d'exemplaires
    $stmt = $db->prepare("UPDATE livres SET nombre_exemplaire = nombre_exemplaire - 1 WHERE id = :id");
    $stmt->execute([':id' => $id_livre]);

    header('Location: wishlist.php?msg=Livre+ajouté+avec+succès+à+votre+liste+de+lecture&type=success');
    exit;

} catch (PDOException $e) {
    header('Location: details.php?id=' . $id_livre . '&msg=Erreur+lors+de+l\'ajout&type=error');
    exit;
}
?>