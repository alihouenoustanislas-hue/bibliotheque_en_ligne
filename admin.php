<?php
/**
 * admin.php - Panneau d'administration avec CRUD des livres
 */
require_once 'auth.php';
require_once 'config.php';

// Vérifier l'authentification
requireLogin();

$admin = getCurrentAdmin();
$db = getDBConnection();

// Récupération de tous les livres
$books = [];
$error = '';
$success = '';

if (isset($_GET['success'])) {
    $success = $_GET['success'];
}
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}

if ($db) {
    try {
        $stmt = $db->query("SELECT * FROM livres ORDER BY id DESC");
        $books = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = "Erreur lors de la récupération des livres.";
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
    <title>Administration - Bibliothèque en Ligne</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .admin-header h1 {
            margin: 0;
            font-size: 1.8rem;
        }
        .admin-info {
            font-size: 0.9rem;
            opacity: 0.95;
        }
        .admin-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .table-header {
            padding: 1.5rem;
            background: var(--bg-light);
            border-bottom: 2px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .table-header h2 {
            margin: 0;
            color: var(--text-dark);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead {
            background: var(--primary);
            color: white;
        }
        th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }
        td {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
        }
        tbody tr:hover {
            background: var(--bg-light);
        }
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        .btn-edit {
            background: var(--info);
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .btn-delete {
            background: var(--danger);
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
        }
        .btn-delete:hover {
            background: #c82333;
        }
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
        }
        @media (max-width: 768px) {
            .table-container {
                overflow-x: auto;
            }
            table {
                min-width: 800px;
            }
        }
    </style>
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
                <li><a href="admin.php" class="active">Administration</a></li>
            </ul>
        </div>
    </header>

    <main>
        <div class="admin-header">
            <div>
                <h1>🛠️ Panneau d'Administration</h1>
                <div class="admin-info">
                    Connecté en tant que : <strong><?php echo htmlspecialchars($admin['nom']); ?></strong>
                    (<?php echo htmlspecialchars($admin['username']); ?>)
                </div>
            </div>
            <div class="admin-actions">
                <a href="admin_add.php" class="btn btn-secondary">➕ Ajouter un livre</a>
                <a href="logout.php" class="btn btn-outline" onclick="return confirm('Voulez-vous vraiment vous déconnecter ?');">
                    🚪 Déconnexion
                </a>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">
                ✓ <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">
                ⚠️ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <div class="table-header">
                <h2>📚 Gestion des Livres</h2>
                <div>
                    <strong><?php echo count($books); ?></strong> livre(s) au total
                </div>
            </div>

            <?php if (empty($books)): ?>
                <div class="empty-state">
                    <div class="icon">📭</div>
                    <h3>Aucun livre dans la bibliothèque</h3>
                    <p>Commencez par ajouter votre premier livre</p>
                    <a href="admin_add.php" class="btn btn-primary" style="margin-top:1rem;">➕ Ajouter un livre</a>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            
                            <th>Titre</th>
                            <th>Auteur</th>
                            <th>Maison d'édition</th>
                            <th>Exemplaires</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($book['titre']); ?></strong></td>
                                <td><?php echo htmlspecialchars($book['auteur']); ?></td>
                                <td><?php echo htmlspecialchars($book['maison_edition'] ?: 'N/A'); ?></td>
                                <td>
                                    <span style="color: <?php echo $book['nombre_exemplaire'] > 3 ? 'var(--success)' : ($book['nombre_exemplaire'] > 0 ? 'var(--warning)' : 'var(--danger)'); ?>;">
                                        <?php echo $book['nombre_exemplaire']; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="details.php?id=<?php echo $book['id']; ?>" class="btn-edit">👁️ Voir</a>
                                        <a href="admin_edit.php?id=<?php echo $book['id']; ?>" class="btn-edit">✏️ Modifier</a>
                                        <form action="admin_delete.php" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce livre ?');">
                                            <input type="hidden" name="id" value="<?php echo $book['id']; ?>">
                                            <button type="submit" class="btn-delete">🗑️ Supprimer</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>

    <footer class="site-footer">
        <p>📚 Bibliothèque en Ligne &copy; 2026</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
