<?php
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/validations.php';

startSession();
require_login();
$pdo = getPDO();
$page = 'gerer';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM livres WHERE id = :id');
$stmt->execute(['id' => $id]);
$livre = $stmt->fetch();

if (!$livre) {
    setFlash('error', "Ce livre n'existe pas.");
    redirect('create.php');
}

$erreurs = [];
$values = $livre;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values = [
        'titre'             => trim($_POST['titre'] ?? ''),
        'auteur'            => trim($_POST['auteur'] ?? ''),
        'description'       => trim($_POST['description'] ?? ''),
        'maison_edition'    => trim($_POST['maison_edition'] ?? ''),
        'nombre_exemplaire' => trim($_POST['nombre_exemplaire'] ?? ''),
        'couverture'        => $livre['couverture'],
    ];

    $erreurs = [];
    if (!csrf_check($_POST['csrf_token'] ?? null)) {
        $erreurs['general'] = 'Requête invalide. Rechargez la page et réessayez.';
    }
    $erreurs = array_merge($erreurs, validerLivre($values));
    $nouvelleCouverture = null;
    $erreurCouverture = null;
    if (empty($erreurs)) {
        $nouvelleCouverture = enregistrerCouverture($_FILES['couverture'] ?? null, $erreurCouverture);
        if ($erreurCouverture) {
            $erreurs['couverture'] = $erreurCouverture;
        }
    }

    if (empty($erreurs)) {
        $couverture = $nouvelleCouverture ?: $livre['couverture'];
        try {
            $stmt = $pdo->prepare(
                'UPDATE livres SET titre = :titre, auteur = :auteur, description = :description,
                 maison_edition = :maison_edition, nombre_exemplaire = :nombre_exemplaire, couverture = :couverture
                 WHERE id = :id'
            );
            $stmt->execute([
                'titre'             => $values['titre'],
                'auteur'            => $values['auteur'],
                'description'       => $values['description'],
                'maison_edition'    => $values['maison_edition'],
                'nombre_exemplaire' => (int)$values['nombre_exemplaire'],
                'couverture'        => $couverture ?: null,
                'id'                => $id,
            ]);
            if ($nouvelleCouverture && $livre['couverture'] !== $nouvelleCouverture) {
                supprimerCouverture($livre['couverture']);
            }

            setFlash('success', 'Le livre « ' . $values['titre'] . ' » a été mis à jour.');
            redirect('create.php');
        } catch (PDOException $e) {
            if ($nouvelleCouverture) {
                supprimerCouverture($nouvelleCouverture);
            }
            $erreurs['general'] = 'Le livre n’a pas pu être modifié. Réessayez plus tard.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Modifier « <?= h($livre['titre']) ?> » · Bibliothèque en ligne</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style/style.css">
</head>
<body>

<?php include __DIR__ . '/partials/header.php'; ?>

<main class="container">
    <p class="breadcrumb"><a href="create.php">&larr; Retour à la gestion des livres</a></p>
    <h1>Modifier « <?= h($livre['titre']) ?> »</h1>

    <section class="admin-card">
        <form action="edit.php?id=<?= (int)$id ?>" method="post" enctype="multipart/form-data" novalidate>
            <input type="hidden" name="id" value="<?= (int)$id ?>">
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
            <?php $resumeErreurs = erreursGenerales($erreurs); ?>
            <?php if ($resumeErreurs): ?>
                <div class="error-summary" role="alert">
                    <strong>Veuillez corriger les éléments suivants :</strong>
                    <ul><?php foreach ($resumeErreurs as $message): ?><li><?= h($message) ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="titre">Titre *</label>
                <input type="text" id="titre" name="titre" value="<?= h($values['titre']) ?>" required>
                <?php if (!empty($erreurs['titre'])): ?><p class="form-error"><?= h($erreurs['titre']) ?></p><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="auteur">Auteur *</label>
                <input type="text" id="auteur" name="auteur" value="<?= h($values['auteur']) ?>" required>
                <?php if (!empty($erreurs['auteur'])): ?><p class="form-error"><?= h($erreurs['auteur']) ?></p><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description"><?= h($values['description']) ?></textarea>
                <?php if (!empty($erreurs['description'])): ?><p class="form-error"><?= h($erreurs['description']) ?></p><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="maison_edition">Maison d'édition</label>
                <input type="text" id="maison_edition" name="maison_edition" value="<?= h($values['maison_edition']) ?>">
                <?php if (!empty($erreurs['maison_edition'])): ?><p class="form-error"><?= h($erreurs['maison_edition']) ?></p><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="nombre_exemplaire">Nombre d'exemplaires *</label>
                <input type="number" min="0" id="nombre_exemplaire" name="nombre_exemplaire" value="<?= h($values['nombre_exemplaire']) ?>" required>
                <?php if (!empty($erreurs['nombre_exemplaire'])): ?><p class="form-error"><?= h($erreurs['nombre_exemplaire']) ?></p><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="couverture">Remplacer la couverture (optionnel)</label>
                <input type="file" id="couverture" name="couverture" accept="image/jpeg,image/png,image/gif,image/webp">
                <div id="couverture-preview" class="cover-preview" <?= empty($livre['couverture']) ? 'hidden' : '' ?>>
                    <img src="<?= h(couvertureUrl($livre['couverture'])) ?>" alt="Aperçu de la couverture actuelle">
                 </div>
                <?php if (!empty($erreurs['couverture'])): ?><p class="form-error"><?= h($erreurs['couverture']) ?></p><?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                <a class="btn btn-secondary" href="create.php">Annuler</a>
            </div>
        </form>
    </section>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>
<script src="js/app.js"></script>
</body>
</html>
