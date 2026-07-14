<?php
/**
 * logout.php - Déconnexion de l'administrateur
 */
require_once 'auth.php';

logout();
header('Location: login.php?msg=Vous avez été déconnecté avec succès');
exit;
?>
