<?php
/**
 * results.php - Page de résultats de recherche
 * Affiche les livres correspondant à la recherche ou tous les livres
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

// Récupération du terme de recherche
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

// Connexion à la base de données
$db = getDBConnection();
$books = [];
$error = '';

if ($db) {
    try {
        if (!empty($search)) {
            // Recherche par titre ou auteur (avec LIKE pour recherche partielle)
            $stmt = $db->prepare("SELECT * FROM livres WHERE titre LIKE :search1 OR auteur LIKE :search2 ORDER BY titre ASC");
            $searchParam = '%' . $search . '%';
            $stmt->execute([':search1' => $searchParam, ':search2' => $searchParam]);
        } else {
            // Afficher tous les livres
            $stmt = $db->query("SELECT * FROM livres ORDER BY titre ASC");
        }
        $books = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = "Erreur lors de la récupération des livres : " . $e->getMessage();
        error_log($error);
    }
} else {
    $error = "Impossible de se connecter à la base de données. Vérifiez votre configuration.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats de recherche - Bibliothèque en Ligne</title>
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
                <li><a href="results.php" class="active">Catalogue</a></li>
                <li><a href="wishlist.php">Ma Liste</a></li>
                <li><a href="login.php">Administration</a></li>
            </ul>
        </div>
    </header>

    <main>
        <h1 class="page-title">🔍 Résultats de recherche</h1>
        <p class="page-subtitle">
            <?php if (!empty($search)): ?>
                Recherche pour : <strong>"<?php echo htmlspecialchars($search); ?>"</strong> 
                (<?php echo count($books); ?> résultat(s))
            <?php else: ?>
                Tous les livres de notre collection
            <?php endif; ?>
        </p>

        <?php if ($error): ?>
            <div class="alert alert-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="search-container">
            <form action="results.php" method="GET" class="search-box" data-validate>
                <input 
                    type="text" 
                    name="q" 
                    id="live-search"
                    value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Rechercher par titre ou auteur..." 
                    aria-label="Rechercher un livre"
                >
                <button type="submit">🔍 Rechercher</button>
            </form>
        </div>

        <?php if (empty($books) && empty($error)): ?>
            <div class="empty-state">
                <div class="icon">📭</div>
                <h3>Aucun livre trouvé</h3>
                <p>Essayez avec d'autres termes de recherche ou consultez le catalogue complet.</p>
                <a href="results.php" class="btn btn-primary" style="margin-top:1rem;">Voir tout le catalogue</a>
            </div>
        <?php else: ?>
            <div class="card-grid">
                <?php foreach ($books as $book): ?>
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
                            <div class="book-meta">
                                <?php echo htmlspecialchars($book['maison_edition']); ?> — 
                                <?php echo $book['nombre_exemplaire']; ?> exemplaire(s)
                            </div>
                            <div class="book-actions">
                                <a href="details.php?id=<?php echo $book['id']; ?>" class="btn btn-primary btn-sm">Voir détails</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer class="site-footer">
        <p>📚 Bibliothèque en Ligne &copy; 2026 — Projet Développement Web Niveau Intermédiaire</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>