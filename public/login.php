<?php
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/auth.php';

startSession();
$pdo = getPDO();

if (is_logged_in()) {
    redirect('index.php?page=accueil');
}

$erreurs = [];
$flash = getFlash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf_token'] ?? null)) {
        $erreurs['general'] = 'Requête invalide. Rechargez la page et réessayez.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $motDePasse = $_POST['password'] ?? '';
        try {
            $stmt = $pdo->prepare('SELECT id, prenom, email, mot_de_passe FROM lecteurs WHERE email = :email');
            $stmt->execute(['email' => $email]);
            $lecteur = $stmt->fetch();
        } catch (PDOException $e) {
            $lecteur = null;
            $erreurs['general'] = 'La connexion est temporairement indisponible. Réessayez plus tard.';
        }

        if (empty($erreurs) && $lecteur && !empty($lecteur['mot_de_passe']) && password_verify($motDePasse, $lecteur['mot_de_passe'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$lecteur['id'];
            $_SESSION['user_email'] = $lecteur['email'];
            $_SESSION['user_prenom'] = $lecteur['prenom'];
            setFlash('success', 'Connexion réussie.');
            redirect('index.php?page=accueil');
        }

        if (empty($erreurs)) {
            $erreurs['general'] = 'Email ou mot de passe incorrect.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion · Bibliothèque</title>
<link rel="stylesheet" href="style/style.css">
</head>
<body>
<main class="container auth-page">
    <section class="admin-card">
        <h1>Connexion</h1>
        <?php if ($flash): ?><div class="alert alert-<?= h($flash['type'] === 'success' ? 'success' : 'error') ?>"><?= h($flash['message']) ?></div><?php endif; ?>
        <?php $resumeErreurs = erreursGenerales($erreurs); ?>
        <?php if ($resumeErreurs): ?>
            <div class="error-summary" role="alert">
                <strong>Connexion impossible :</strong>
                <ul><?php foreach ($resumeErreurs as $message): ?><li><?= h($message) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>
        <form method="post" action="login.php">
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            <span>Identifiants pour tester l'application : <strong>email= test@email.com / mot de passe= admin123</strong></span>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Se connecter</button>
                <a class="btn btn-secondary" href="register.php">Créer un compte</a>
                <a class="btn btn-secondary" href="index.php?page=accueil">Retour à l’accueil</a>
            </div>
        </form>
    </section>
</main>
</body>
</html>