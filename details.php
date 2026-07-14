<?php
/**
 * details.php - Page de détails d'un livre
 * Affiche les informations complètes d'un livre sélectionné
 */
require_once 'config.php';

// Récupération de l'ID du livre
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = isset($_GET['msg']) ? $_GET['msg'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : 'info';

$book = null;
$error = '';

if ($id > 0) {
    $db = getDBConnection();
    if ($db) {
        try {
            $stmt = $db->prepare("SELECT * FROM livres WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $book = $stmt->fetch();

            if (!$book) {
                $error = "Ce livre n'existe pas dans notre collection.";
            }
        } catch (PDOException $e) {
            $error = "Erreur lors de la récupération du livre.";
        }
    } else {
        $error = "Impossible de se connecter à la base de données.";
    }
} else {
    $error = "Aucun livre sélectionné.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $book ? htmlspecialchars($book['titre']) : 'Détails'; ?> - Bibliothèque en Ligne</title>
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
                <li><a href="wishlist.php">Ma Liste</a></li>
                <li><a href="login.php">Administration</a></li>
            </ul>
        </div>
    </header>

    <main>
        <h1 class="page-title">📖 Détails du livre</h1>
        <p class="page-subtitle">Consultez les informations complètes et empruntez ce livre</p>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo htmlspecialchars($type); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
            <div style="text-align: center; margin-top: 2rem;">
                <a href="results.php" class="btn btn-primary">← Retour au catalogue</a>
            </div>
        <?php elseif ($book): ?>
            <div class="detail-container">
                <div class="detail-cover">📖</div>
                <div class="detail-info">
                    <h2><?php echo htmlspecialchars($book['titre']); ?></h2>
                    <p class="author">par <?php echo htmlspecialchars($book['auteur']); ?></p>

                    <p class="description">
                        <?php echo nl2br(htmlspecialchars($book['description'] ?: 'Aucune description disponible pour ce livre.')); ?>
                    </p>

                    <div class="detail-meta">
                        <div class="meta-item">
                            <div class="meta-label">Maison d'édition</div>
                            <div class="meta-value"><?php echo htmlspecialchars($book['maison_edition'] ?: 'Non spécifiée'); ?></div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label">Exemplaires disponibles</div>
                            <div class="meta-value" style="color: <?php echo $book['nombre_exemplaire'] > 0 ? 'var(--success)' : 'var(--danger)'; ?>;">
                                <?php echo $book['nombre_exemplaire']; ?>
                            </div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label">Référence</div>
                            <div class="meta-value">#<?php echo str_pad($book['id'], 3, '0', STR_PAD_LEFT); ?></div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label">Statut</div>
                            <div class="meta-value" style="color: <?php echo $book['nombre_exemplaire'] > 0 ? 'var(--success)' : 'var(--danger)'; ?>;">
                                <?php echo $book['nombre_exemplaire'] > 0 ? '✓ Disponible' : '✗ Indisponible'; ?>
                            </div>
                        </div>
                    </div>

                    <div class="book-actions">
                        <?php if ($book['nombre_exemplaire'] > 0): ?>
                            <a href="wishlist_add.php?id=<?php echo $book['id']; ?>" class="btn btn-secondary add-to-wishlist" data-book-id="<?php echo $book['id']; ?>">
                                ➕ Ajouter à ma liste
                            </a>
                        <?php else: ?>
                            <button class="btn btn-outline" disabled>❌ Plus disponible</button>
                        <?php endif; ?>
                        <a href="results.php" class="btn btn-outline">← Retour au catalogue</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <footer class="site-footer">
        <p>📚 Bibliothèque en Ligne &copy; 2026 — Projet Développement Web Niveau Intermédiaire</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>