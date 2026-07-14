<?php
/**
 * admin_add.php - Formulaire d'ajout d'un livre
 */
require_once 'auth.php';
require_once 'config.php';

requireLogin();

$error = '';
$success = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = isset($_POST['titre']) ? trim($_POST['titre']) : '';
    $auteur = isset($_POST['auteur']) ? trim($_POST['auteur']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $maison_edition = isset($_POST['maison_edition']) ? trim($_POST['maison_edition']) : '';
    $nombre_exemplaire = isset($_POST['nombre_exemplaire']) ? intval($_POST['nombre_exemplaire']) : 1;

    // Validation
    if (empty($titre) || empty($auteur)) {
        $error = 'Le titre et l\'auteur sont obligatoires';
    } elseif ($nombre_exemplaire < 0) {
        $error = 'Le nombre d\'exemplaires ne peut pas être négatif';
    } else {
        $db = getDBConnection();
        if ($db) {
            try {
                $stmt = $db->prepare("INSERT INTO livres (titre, auteur, description, maison_edition, nombre_exemplaire) VALUES (:titre, :auteur, :description, :maison_edition, :nombre_exemplaire)");
                $stmt->execute([
                    ':titre' => $titre,
                    ':auteur' => $auteur,
                    ':description' => $description,
                    ':maison_edition' => $maison_edition,
                    ':nombre_exemplaire' => $nombre_exemplaire
                ]);
                header('Location: admin.php?success=' . urlencode('Livre ajouté avec succès'));
                exit;
            } catch (PDOException $e) {
                $error = 'Erreur lors de l\'ajout du livre';
            }
        } else {
            $error = 'Erreur de connexion à la base de données';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un livre - Administration</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-container {
            max-width: 700px;
            margin: 2rem auto;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border);
        }
        .form-header h1 {
            color: var(--primary);
            margin: 0 0 0.5rem 0;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-dark);
        }
        .form-group label .required {
            color: var(--danger);
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 1rem;
            font-family: inherit;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
        }
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
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
            <ul class="nav-links">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="results.php">Catalogue</a></li>
                <li><a href="admin.php" class="active">Administration</a></li>
            </ul>
        </div>
    </header>

    <main>
        <div class="form-container">
            <div class="form-header">
                <h1>➕ Ajouter un nouveau livre</h1>
                <p>Remplissez les informations du livre à ajouter à la bibliothèque</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    ⚠️ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="admin_add.php">
                <div class="form-group">
                    <label for="titre">
                        Titre du livre <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="titre" 
                        name="titre" 
                        required 
                        placeholder="Ex: Le Petit Prince"
                        value="<?php echo isset($_POST['titre']) ? htmlspecialchars($_POST['titre']) : ''; ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="auteur">
                        Auteur <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="auteur" 
                        name="auteur" 
                        required 
                        placeholder="Ex: Antoine de Saint-Exupéry"
                        value="<?php echo isset($_POST['auteur']) ? htmlspecialchars($_POST['auteur']) : ''; ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="description">
                        Description
                    </label>
                    <textarea 
                        id="description" 
                        name="description" 
                        placeholder="Résumé ou description du livre..."
                    ><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="maison_edition">
                        Maison d'édition
                    </label>
                    <input 
                        type="text" 
                        id="maison_edition" 
                        name="maison_edition" 
                        placeholder="Ex: Gallimard"
                        value="<?php echo isset($_POST['maison_edition']) ? htmlspecialchars($_POST['maison_edition']) : ''; ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="nombre_exemplaire">
                        Nombre d'exemplaires <span class="required">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="nombre_exemplaire" 
                        name="nombre_exemplaire" 
                        required 
                        min="0"
                        value="<?php echo isset($_POST['nombre_exemplaire']) ? htmlspecialchars($_POST['nombre_exemplaire']) : '1'; ?>"
                    >
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        ✓ Ajouter le livre
                    </button>
                    <a href="admin.php" class="btn btn-outline">
                        ✗ Annuler
                    </a>
                </div>
            </form>
        </div>
    </main>

    <footer class="site-footer">
        <p>📚 Bibliothèque en Ligne &copy; 2026</p>
    </footer>
</body>
</html>
