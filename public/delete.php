<?php
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/auth.php';

startSession();
require_login();
$pdo = getPDO();

$id = (int)($_POST['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_check($_POST['csrf_token'] ?? null)) {
    setFlash('error', 'Requête invalide.');
} elseif ($id > 0) {
    $stmt = $pdo->prepare('SELECT titre, couverture FROM livres WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $livre = $stmt->fetch();

    if ($livre) {
        $stmt = $pdo->prepare('DELETE FROM livres WHERE id = :id');
        $stmt->execute(['id' => $id]);
        supprimerCouverture($livre['couverture']);

        // Le livre est aussi retiré de la liste de lecture en session, s'il y était.
        removeFromWishlist($pdo, $id);

        setFlash('success', 'Le livre « ' . $livre['titre'] . ' » a été supprimé.');
    } else {
        setFlash('error', "Ce livre n'existe pas ou a déjà été supprimé.");
    }
} else {
    setFlash('error', 'Identifiant de livre invalide.');
}

redirect('create.php');
