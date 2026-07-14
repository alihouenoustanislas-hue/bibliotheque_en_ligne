<?php
/**
 * wishlist.php - Page de liste de lecture
 * Affiche et gère les livres empruntés par un lecteur
 */
require_once 'config.php';

// ID du lecteur connecté (dans un vrai système, viendrait de la session)
$lecteur_id = isset($_GET['lecteur']) ? intval($_GET['lecteur']) : 1;

$db = getDBConnection();
$wishlist = [];
$lecteur = null;
$error = '';
$message = isset($_GET['msg']) ? $_GET['msg'] : '';
$msgType = isset($_GET['type']) ? $_GET['type'] : 'info';

if ($db) {
    try {
        // Récupérer les infos du lecteur
        $stmt = $db->prepare("SELECT * FROM lecteurs WHERE id = :id");
        $stmt->execute([':id' => $lecteur_id]);
        $lecteur = $stmt->fetch();

        // Récupérer la liste de lecture avec les infos des livres
        $stmt = $db->prepare("
            SELECT l.*, ll.date_emprunt, ll.date_retour, 
                   DATEDIFF(ll.date_retour, CURDATE()) as jours_restants
            FROM liste_lecture ll
            JOIN livres l ON ll.id_livre = l.id
            WHERE ll.id_lecteur = :lecteur_id
            ORDER BY ll.date_emprunt DESC
        ");
        $stmt->execute([':lecteur_id' => $lecteur_id]);
        $wishlist = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = "Erreur lors de la récupération de la liste de lecture.";
    }
} else {
    $error = "Impossible de se connecter à la base de données.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ma Liste de Lecture - Bibliothèque en Ligne</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="site-header">
        <div class="nav-container">
            <a href="index.php" class="logo">
                <span class="logo-icon">📚</span>
                <span>BiblioLigne</span>
            </a>
            <button class="menu-toggle" aria-label="Menu">☰</button>
            <ul class="nav-links">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="results.php">Catalogue</a></li>
                <li><a href="wishlist.php" class="active">Ma Liste</a></li>
                <li><a href="login.php">Administration</a></li>
            </ul>
        </div>
    </header>

    <main>
        <h1 class="page-title">📋 Ma Liste de Lecture</h1>
        <p class="page-subtitle">
            <?php if ($lecteur): ?>
                Liste de <?php echo htmlspecialchars($lecteur['prenom'] . ' ' . $lecteur['nom']); ?>
            <?php else: ?>
                Gérez les livres que vous avez empruntés ou souhaitez lire
            <?php endif; ?>
        </p>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo htmlspecialchars($msgType); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (empty($wishlist) && empty($error)): ?>
            <div class="empty-state">
                <div class="icon">📭</div>
                <h3>Votre liste de lecture est vide</h3>
                <p>Explorez notre catalogue et ajoutez des livres à votre liste !</p>
                <a href="results.php" class="btn btn-primary" style="margin-top:1rem;">📚 Explorer le catalogue</a>
            </div>
        <?php elseif (!empty($wishlist)): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Livre</th>
                        <th>Auteur</th>
                        <th>Date d'emprunt</th>
                        <th>Date de retour</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($wishlist as $item): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($item['titre']); ?></strong></td>
                            <td><?php echo htmlspecialchars($item['auteur']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($item['date_emprunt'])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($item['date_retour'])); ?></td>
                            <td>
                                <?php if ($item['jours_restants'] >= 0): ?>
                                    <span style="color: var(--success);">📗 <?php echo $item['jours_restants']; ?> jour(s) restant(s)</span>
                                <?php else: ?>
                                    <span style="color: var(--danger);">📕 En retard de <?php echo abs($item['jours_restants']); ?> jour(s)</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="details.php?id=<?php echo $item['id']; ?>" class="btn btn-primary btn-sm">📖 Voir</a>
                                <a href="wishlist_remove.php?id_livre=<?php echo $item['id']; ?>&id_lecteur=<?php echo $lecteur_id; ?>" 
                                   class="btn btn-danger btn-sm confirm-delete">
                                    🗑️ Retirer
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div style="margin-top: 2rem; text-align: center;">
                <a href="results.php" class="btn btn-primary">📚 Explorer le catalogue</a>
            </div>
        <?php endif; ?>
    </main>

    <footer class="site-footer">
        <p>📚 Bibliothèque en Ligne &copy; 2026 — Projet Développement Web Niveau Intermédiaire</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>