<?php
/**
 * wishlist_remove.php - Retire un livre de la liste de lecture
 */
require_once 'config.php';

$id_livre = isset($_GET['id_livre']) ? intval($_GET['id_livre']) : 0;
$id_lecteur = isset($_GET['id_lecteur']) ? intval($_GET['id_lecteur']) : 0;

if ($id_livre <= 0 || $id_lecteur <= 0) {
    header('Location: wishlist.php?msg=Paramètres+invalides&type=error');
    exit;
}

$db = getDBConnection();
if (!$db) {
    header('Location: wishlist.php?msg=Erreur+de+connexion+base+de+données&type=error');
    exit;
}

try {
    // Supprimer de la liste de lecture
    $stmt = $db->prepare("
        DELETE FROM liste_lecture 
        WHERE id_livre = :id_livre AND id_lecteur = :id_lecteur
    ");
    $stmt->execute([':id_livre' => $id_livre, ':id_lecteur' => $id_lecteur]);

    // Incrémenter le nombre d'exemplaires
    $stmt = $db->prepare("UPDATE livres SET nombre_exemplaire = nombre_exemplaire + 1 WHERE id = :id");
    $stmt->execute([':id' => $id_livre]);

    header('Location: wishlist.php?msg=Livre+retiré+de+votre+liste+avec+succès&type=success');
    exit;

} catch (PDOException $e) {
    header('Location: wishlist.php?msg=Erreur+lors+de+la+suppression&type=error');
    exit;
}
?>