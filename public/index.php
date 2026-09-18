<?php
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/auth.php';

startSession();
$pdo = getPDO();

// Routage simple basé sur le paramètre GET "page"
$page = $_GET['page'] ?? 'accueil';
$flash = getFlash();

/* ---------------------------------------------------------
 * Actions (ajout / retrait de la liste de lecture)
 * --------------------------------------------------------- */
if ($page === 'wishlist-add' && isset($_GET['id'])) {
    require_login();
    addToWishlist($pdo, (int)$_GET['id']);
    redirect($_GET['retour'] ?? 'index.php?page=liste');
}
if ($page === 'wishlist-remove' && isset($_GET['id'])) {
    require_login();
    removeFromWishlist($pdo, (int)$_GET['id']);
    redirect('index.php?page=liste');
}

/* ---------------------------------------------------------
 * Données nécessaires selon la page demandée
 * --------------------------------------------------------- */
$livresSuggestions = [];
$resultats = [];
$termeRecherche = trim($_GET['q'] ?? '');
$livreDetail = null;
$wishlistLivres = [];

if ($page === 'accueil') {
    $stmt = $pdo->query('SELECT * FROM livres ORDER BY date_ajout DESC LIMIT 4');
    $livresSuggestions = $stmt->fetchAll();

} elseif ($page === 'recherche') {
    if ($termeRecherche !== '') {
        $stmt = $pdo->prepare(
            'SELECT * FROM livres WHERE titre LIKE :terme1 OR auteur LIKE :terme2 ORDER BY titre'
        );
        $stmt->execute([
            'terme1' => '%' . $termeRecherche . '%',
            'terme2' => '%' . $termeRecherche . '%',
        ]);
        $resultats = $stmt->fetchAll();
    } else {
        $resultats = $pdo->query('SELECT * FROM livres ORDER BY titre')->fetchAll();
    }

} elseif ($page === 'details') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare('SELECT * FROM livres WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $livreDetail = $stmt->fetch();

    if (!$livreDetail) {
        setFlash('error', "Ce livre n'existe pas ou a été supprimé.");
        redirect('index.php?page=accueil');
    }

} elseif ($page === 'liste') {
    require_login();
    $ids = getWishlist($pdo);
    if (!empty($ids)) {
        $in = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT * FROM livres WHERE id IN ($in)");
        $stmt->execute($ids);
        $wishlistLivres = $stmt->fetchAll();
    }
}

$pageTitre = [
    'accueil'   => 'Accueil',
    'recherche' => 'Rechercher',
    'details'   => $livreDetail['titre'] ?? 'Détails du livre',
    'liste'     => 'Ma liste de lecture',
][$page] ?? 'Bibliothèque';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($pageTitre) ?> · Bibliothèque en ligne</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style/style.css">
</head>
<body>

<?php include __DIR__ . '/partials/header.php'; ?>

<main class="container">

    <?php if ($flash): ?>
        <div class="alert alert-<?= h($flash['type'] === 'success' ? 'success' : 'error') ?>">
            <?= h($flash['message']) ?>
        </div>
    <?php endif; ?>

    <?php if ($page === 'accueil'): ?>
        <?php include __DIR__ . '/partials/page-accueil.php'; ?>

    <?php elseif ($page === 'recherche'): ?>
        <?php include __DIR__ . '/partials/page-recherche.php'; ?>

    <?php elseif ($page === 'details'): ?>
        <?php include __DIR__ . '/partials/page-details.php'; ?>

    <?php elseif ($page === 'liste'): ?>
        <?php include __DIR__ . '/partials/page-liste.php'; ?>

    <?php else: ?>
        <div class="empty-state"><h2>Page introuvable</h2></div>
    <?php endif; ?>

</main>

<?php include __DIR__ . '/partials/footer.php'; ?>

<script src="js/app.js"></script>
</body>
</html>
