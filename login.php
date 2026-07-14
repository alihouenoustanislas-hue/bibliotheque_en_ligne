<?php
/**
 * login.php - Page de connexion administrateur
 */
require_once 'auth.php';

// Si déjà connecté, rediriger vers admin
if (isLoggedIn()) {
    header('Location: admin.php');
    exit;
}

$error = '';
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'admin.php';

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($username) || empty($password)) {
        $error = 'Veuillez remplir tous les champs';
    } else {
        $result = login($username, $password);
        if ($result['success']) {
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Administration</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 4rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header h1 {
            font-size: 1.8rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
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
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 1rem;
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
        }
        .login-info {
            background: var(--bg-light);
            padding: 1rem;
            border-radius: 4px;
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }
        .login-info strong {
            color: var(--primary);
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
            </ul>
        </div>
    </header>

    <main>
        <div class="login-container">
            <div class="login-header">
                <h1>🔐 Connexion Administrateur</h1>
                <p>Accédez au panneau d'administration</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    ⚠️ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        required 
                        autofocus
                        value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Se connecter
                </button>
            </form>

            <div class="login-info">
                <strong>💡 Compte de test :</strong><br>
                Username: <code>admin</code><br>
                Password: <code>admin123</code>
            </div>

            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="index.php" class="btn btn-outline">← Retour à l'accueil</a>
            </div>
        </div>
    </main>

    <footer class="site-footer">
        <p>📚 Bibliothèque en Ligne &copy; 2026</p>
    </footer>
</body>
</html>
