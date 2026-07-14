<?php
/**
 * featured_books.php - Retourne les 4 premiers livres pour la page d'accueil
 * Appelé en AJAX ou inclus
 */
require_once 'config.php';

$db = getDBConnection();
if (!$db) {
    echo '<div class="alert alert-error">Impossible de charger les livres en vedette.</div>';
    exit;
}

try {
    $stmt = $db->query("SELECT * FROM livres ORDER BY id ASC LIMIT 4");
    $books = $stmt->fetchAll();

    foreach ($books as $book):
?>
        <div class="book-card">
            <div class="book-cover">
                📖
                <?php if ($book['nombre_exemplaire'] > 3): ?>
                    <span class="badge"><?php echo $book['nombre_exemplaire']; ?> disp.</span>
                <?php else: ?>
                    <span class="badge low"><?php echo $book['nombre_exemplaire']; ?> disp.</span>
                <?php endif; ?>
            </div>
            <div class="book-info">
                <div class="book-title"><?php echo htmlspecialchars($book['titre']); ?></div>
                <div class="book-author"><?php echo htmlspecialchars($book['auteur']); ?></div>
                <div class="book-meta"><?php echo htmlspecialchars($book['maison_edition']); ?></div>
                <div class="book-actions">
                    <a href="details.php?id=<?php echo $book['id']; ?>" class="btn btn-primary btn-sm">Voir détails</a>
                </div>
            </div>
        </div>
<?php
    endforeach;
} catch (PDOException $e) {
    echo '<div class="alert alert-error">Erreur de chargement.</div>';
}
?>