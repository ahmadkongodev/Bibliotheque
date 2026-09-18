<?php
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/auth.php';

startSession();
$pdo = getPDO();

if (is_logged_in()) {
    redirect('index.php?page=accueil');
}

$erreurs = [];
$values = ['nom' => '', 'prenom' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values = [
        'nom' => trim($_POST['nom'] ?? ''),
        'prenom' => trim($_POST['prenom'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
    ];
    $motDePasse = $_POST['password'] ?? '';
    $confirmation = $_POST['password_confirmation'] ?? '';

    if (!csrf_check($_POST['csrf_token'] ?? null)) {
        $erreurs['general'] = 'Requête invalide. Rechargez la page et réessayez.';
    }
    if ($values['nom'] === '') {
        $erreurs['nom'] = 'Le nom est obligatoire.';
    }
    if ($values['prenom'] === '') {
        $erreurs['prenom'] = 'Le prénom est obligatoire.';
    }
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $erreurs['email'] = 'Saisissez une adresse email valide.';
    }
    if (strlen($motDePasse) < 8) {
        $erreurs['password'] = 'Le mot de passe doit contenir au moins 8 caractères.';
    } elseif ($motDePasse !== $confirmation) {
        $erreurs['password_confirmation'] = 'Les mots de passe ne correspondent pas.';
    }

    if (empty($erreurs)) {
        try {
            $stmt = $pdo->prepare('SELECT id FROM lecteurs WHERE email = :email');
            $stmt->execute(['email' => $values['email']]);
            if ($stmt->fetch()) {
                $erreurs['email'] = 'Cette adresse email est déjà utilisée.';
            }
        } catch (PDOException $e) {
            $erreurs['general'] = 'Le compte ne peut pas être vérifié pour le moment. Réessayez plus tard.';
        }
    }

    if (empty($erreurs)) {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO lecteurs (nom, prenom, email, mot_de_passe)
                 VALUES (:nom, :prenom, :email, :mot_de_passe)'
            );
            $stmt->execute([
                'nom' => $values['nom'],
                'prenom' => $values['prenom'],
                'email' => $values['email'],
                'mot_de_passe' => password_hash($motDePasse, PASSWORD_DEFAULT),
            ]);

            setFlash('success', 'Compte créé. Vous pouvez maintenant vous connecter.');
            redirect('login.php');
        } catch (PDOException $e) {
            $erreurs['general'] = 'Le compte n’a pas pu être créé. Réessayez plus tard.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Créer un compte · Bibliothèque</title>
<link rel="stylesheet" href="style/style.css">
</head>
<body>
<main class="container auth-page">
    <section class="admin-card">
        <h1>Créer un compte</h1>
        <?php $resumeErreurs = erreursGenerales($erreurs); ?>
        <?php if ($resumeErreurs): ?>
            <div class="error-summary" role="alert">
                <strong>Veuillez corriger les éléments suivants :</strong>
                <ul><?php foreach ($resumeErreurs as $message): ?><li><?= h($message) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>
        <form method="post" action="register.php">
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" value="<?= h($values['prenom']) ?>" required>
                <?php if (!empty($erreurs['prenom'])): ?><p class="form-error"><?= h($erreurs['prenom']) ?></p><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" value="<?= h($values['nom']) ?>" required>
                <?php if (!empty($erreurs['nom'])): ?><p class="form-error"><?= h($erreurs['nom']) ?></p><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= h($values['email']) ?>" required>
                <?php if (!empty($erreurs['email'])): ?><p class="form-error"><?= h($erreurs['email']) ?></p><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" minlength="8" required>
                <?php if (!empty($erreurs['password'])): ?><p class="form-error"><?= h($erreurs['password']) ?></p><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input type="password" id="password_confirmation" name="password_confirmation" minlength="8" required>
                <?php if (!empty($erreurs['password_confirmation'])): ?><p class="form-error"><?= h($erreurs['password_confirmation']) ?></p><?php endif; ?>
            </div>
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