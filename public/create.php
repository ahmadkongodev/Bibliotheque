<?php
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/validations.php';

startSession();
require_login();
$pdo = getPDO();
$page = 'gerer';
$erreurs = [];
$values = ['titre' => '', 'auteur' => '', 'description' => '', 'maison_edition' => '', 'nombre_exemplaire' => '', 'couverture' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values = [
        'titre'             => trim($_POST['titre'] ?? ''),
        'auteur'            => trim($_POST['auteur'] ?? ''),
        'description'       => trim($_POST['description'] ?? ''),
        'maison_edition'    => trim($_POST['maison_edition'] ?? ''),
        'nombre_exemplaire' => trim($_POST['nombre_exemplaire'] ?? ''),
        'couverture'        => null,
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
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO livres (titre, auteur, description, maison_edition, nombre_exemplaire, couverture)
                 VALUES (:titre, :auteur, :description, :maison_edition, :nombre_exemplaire, :couverture)'
            );
            $stmt->execute([
                'titre'             => $values['titre'],
                'auteur'            => $values['auteur'],
                'description'       => $values['description'],
                'maison_edition'    => $values['maison_edition'],
                'nombre_exemplaire' => (int)$values['nombre_exemplaire'],
                'couverture'        => $nouvelleCouverture,
            ]);

            setFlash('success', 'Le livre « ' . $values['titre'] . ' » a été ajouté avec succès.');
            redirect('create.php');
        } catch (PDOException $e) {
            if ($nouvelleCouverture) {
                supprimerCouverture($nouvelleCouverture);
            }
            $erreurs['general'] = 'Le livre n’a pas pu être ajouté. Réessayez plus tard.';
        }
    }
}

$livres = $pdo->query('SELECT * FROM livres ORDER BY date_ajout DESC')->fetchAll();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gérer les livres · Bibliothèque en ligne</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style/style.css">
</head>
<body>

<?php include __DIR__ . '/partials/header.php'; ?>

<main class="container">
    <h1>Gérer les livres</h1>
    <p class="section__subtitle">Ajoutez un nouveau livre au catalogue ou modifiez / supprimez un livre existant.</p>

    <?php if ($flash): ?>
        <div class="alert alert-<?= h($flash['type'] === 'success' ? 'success' : 'error') ?>"><?= h($flash['message']) ?></div>
    <?php endif; ?>
    <?php $resumeErreurs = erreursGenerales($erreurs); ?>
    <?php if ($resumeErreurs): ?>
        <div class="error-summary" role="alert">
            <strong>Le formulaire contient des erreurs :</strong>
            <ul><?php foreach ($resumeErreurs as $message): ?><li><?= h($message) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <section class="admin-card">
        <h2>Ajouter un livre</h2>
        <form action="create.php" method="post" enctype="multipart/form-data" novalidate>
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
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
                <label for="couverture">Image de couverture (optionnel)</label>
                <input type="file" id="couverture" name="couverture" accept="image/jpeg,image/png,image/gif,image/webp">
                <div id="couverture-preview" class="cover-preview" hidden>
                    <img src="" alt="Aperçu de la couverture sélectionnée">
                </div>
                <?php if (!empty($erreurs['couverture'])): ?><p class="form-error"><?= h($erreurs['couverture']) ?></p><?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Ajouter le livre</button>
            </div>
        </form>
    </section>

    <section class="section">
        <h2>Catalogue (<?= count($livres) ?>)</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Couverture</th>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Édition</th>
                    <th>Exemplaires</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($livres as $livre): ?>
                    <tr>
                        <td>
                            <img class="admin-table__cover" src="<?= h(couvertureUrl($livre['couverture'])) ?>" alt="Couverture de <?= h($livre['titre']) ?>">
                        </td>
                        <td><a href="index.php?page=details&id=<?= (int)$livre['id'] ?>"><?= h($livre['titre']) ?></a></td>
                        <td><?= h($livre['auteur']) ?></td>
                        <td><?= h($livre['maison_edition'] ?: '—') ?></td>
                        <td><?= (int)$livre['nombre_exemplaire'] ?></td>
                        <td class="admin-table__actions">
                            <a class="btn btn-secondary btn-sm" href="edit.php?id=<?= (int)$livre['id'] ?>">Modifier</a>
                            <form method="post" action="delete.php" class="admin-table__delete-form" onsubmit="return confirm('Supprimer « <?= h(addslashes($livre['titre'])) ?> » ? Cette action est irréversible.');">
                                <input type="hidden" name="id" value="<?= (int)$livre['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($livres)): ?>
                    <tr><td colspan="6">Aucun livre enregistré pour le moment.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>
<script src="js/app.js"></script>
</body>
</html>
