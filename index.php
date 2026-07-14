<?php
/**
 * index.php - Page d'accueil avec livres en vedette depuis la base de données
 */
require_once 'config.php';

$db = getDBConnection();
$featured_books = [];

if ($db) {
    try {
        $stmt = $db->query("SELECT * FROM livres ORDER BY id ASC LIMIT 4");
        $featured_books = $stmt->fetchAll();
    } catch (PDOException $e) {
        // En cas d'erreur, les livres en vedette seront vides
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliothèque en Ligne - Accueil</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Header / Navigation -->
    <header class="site-header">
        <div class="nav-container">
            <a href="index.php" class="logo">
                <span class="logo-icon">📚</span>
                <span>BiblioLigne</span>
            </a>
            <button class="menu-toggle" aria-label="Menu">☰</button>
            <ul class="nav-links">
                <li><a href="index.php" class="active">Accueil</a></li>
                <li><a href="results.php">Catalogue</a></li>
                <li><a href="wishlist.php">Ma Liste</a></li>
                <li><a href="login.php">Administration</a></li>
            </ul>
        </div>
    </header>

    <main>
        <!-- Section Hero -->
        <section class="hero">
            <h1>📖 Bienvenue à la Bibliothèque en Ligne</h1>
            <p class="subtitle">
                Découvrez notre vaste collection de livres, empruntez vos favoris et gérez votre liste de lecture personnelle. 
                Une expérience de lecture moderne et intuitive.
            </p>
            <a href="results.php" class="btn btn-primary btn-lg">Explorer le catalogue</a>
        </section>

        <!-- Instructions -->
        <div class="instructions">
            <h3>💡 Comment utiliser le site</h3>
            <ul>
                <li><strong>Rechercher :</strong> Utilisez la barre de recherche ci-dessous ou allez sur la page Catalogue pour trouver un livre par titre ou auteur.</li>
                <li><strong>Voir les détails :</strong> Cliquez sur "Voir les détails" sur n'importe quel livre pour consulter sa fiche complète.</li>
                <li><strong>Ajouter à ma liste :</strong> Sur la page de détails, cliquez sur "Ajouter à ma liste de lecture" pour emprunter un livre.</li>
                <li><strong>Gérer ma liste :</strong> Consultez et retirez des livres de votre liste sur la page "Ma Liste".</li>
                <li><strong>Administrer :</strong> Les administrateurs peuvent ajouter, modifier et supprimer des livres via la page Administration.</li>
            </ul>
        </div>

        <!-- Barre de recherche -->
        <div class="search-container">
            <form action="results.php" method="GET" class="search-box" data-validate>
                <input 
                    type="text" 
                    name="q" 
                    placeholder="Rechercher un livre par titre ou auteur..." 
                    aria-label="Rechercher un livre"
                >
                <button type="submit">🔍 Rechercher</button>
            </form>
        </div>

        <div class="section-divider">
            <h2>📚 Livres en vedette</h2>
        </div>

        <!-- Grille de livres chargée depuis la base de données -->
        <div class="card-grid">
            <?php if (empty($featured_books)): ?>
                <div class="empty-state">
                    <div class="icon">📭</div>
                    <h3>Aucun livre disponible</h3>
                    <p>La bibliothèque est vide pour le moment.</p>
                </div>
            <?php else: ?>
                <?php foreach ($featured_books as $book): ?>
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
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <footer class="site-footer">
        <p>📚 Bibliothèque en Ligne &copy; 2026 — Projet Développement Web Niveau Intermédiaire</p>
        <p>Créé avec HTML, CSS, JavaScript, PHP & MySQL</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
