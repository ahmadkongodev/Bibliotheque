<?php
// Fonctions utilitaires génériques utilisées dans tout le site.


 // Échappe une chaîne pour un affichage HTML sûr.

function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}


// Redirige vers une URL relative au dossier public/ et arrête le script.

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}


// Démarre la session si elle n'est pas déjà active.

function startSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}


// Dépose un message flash (succès/erreur) affiché une seule fois.

function setFlash(string $type, string $message): void
{
    startSession();
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}


// Récupère puis efface le message flash courant, s'il existe.

function getFlash(): ?array
{
    startSession();
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function erreursGenerales(array $erreurs): array
{
    return array_filter(
        $erreurs,
        static fn($champ) => $champ === 'general',
        ARRAY_FILTER_USE_KEY
    );
}


// Retourne l'URL de la couverture d'un livre, ou une image par défaut.

function couvertureUrl(?string $couverture): string
{
    if (!empty($couverture)) {
        return $couverture;
    }
    return COUVERTURE_DEFAUT;
}

// Enregistre une couverture dans le dossier public et retourne son chemin web.

function enregistrerCouverture(?array $fichier, ?string &$erreur): ?string
{
    $erreur = null;

    if (!$fichier || ($fichier['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($fichier['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        $erreur = 'Le téléchargement de la couverture a échoué.';
        return null;
    }

    if (($fichier['size'] ?? 0) > 2 * 1024 * 1024) {
        $erreur = 'La couverture ne doit pas dépasser 2 Mo.';
        return null;
    }

    $typesAutorises = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $type = $finfo ? finfo_file($finfo, $fichier['tmp_name']) : false;
    

    if (!isset($typesAutorises[$type])) {
        $erreur = 'La couverture doit être une image JPG, PNG, GIF ou WebP.';
        return null;
    }

    $dossier = __DIR__ . '/../public/assets/uploads';
    if (!is_dir($dossier) && !mkdir($dossier, 0755, true)) {
        $erreur = 'Le dossier de stockage des couvertures est inaccessible.';
        return null;
    }

    $nom = bin2hex(random_bytes(16)) . '.' . $typesAutorises[$type];
    if (!move_uploaded_file($fichier['tmp_name'], $dossier . '/' . $nom)) {
        $erreur = 'La couverture n’a pas pu être enregistrée.';
        return null;
    }

    return 'assets/uploads/' . $nom;
}

function supprimerCouverture(?string $couverture): void
{
    if (!$couverture || strpos($couverture, 'assets/uploads/') !== 0) {
        return;
    }

    $chemin = __DIR__ . '/../public/' . $couverture;
    if (is_file($chemin)) {
        unlink($chemin);
    }
}


// Tronque un texte à une longueur donnée en ajoutant des points de suspension.

function tronquer(string $texte, int $longueur = 140): string
{
    if (mb_strlen($texte) <= $longueur) {
        return $texte;
    }
    return mb_substr($texte, 0, $longueur) . '…';
}

// Liste de lecture persistante, associee au lecteur connecte.

function getWishlist(PDO $pdo): array
{
    startSession();
    $idLecteur = (int)($_SESSION['user_id'] ?? 0);
    if ($idLecteur <= 0) {
        return [];
    }

    $stmt = $pdo->prepare('SELECT id_livre FROM liste_lecture WHERE id_lecteur = :id_lecteur');
    $stmt->execute(['id_lecteur' => $idLecteur]);
    return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
}

function isInWishlist(PDO $pdo, int $idLivre): bool
{
    return in_array($idLivre, getWishlist($pdo), true);
}

function addToWishlist(PDO $pdo, int $idLivre): void
{
    startSession();
    $idLecteur = (int)($_SESSION['user_id'] ?? 0);
    if ($idLecteur <= 0) {
        return;
    }

    $stmt = $pdo->prepare(
        'INSERT IGNORE INTO liste_lecture (id_livre, id_lecteur)
         VALUES (:id_livre, :id_lecteur)'
    );
    $stmt->execute(['id_livre' => $idLivre, 'id_lecteur' => $idLecteur]);
}

function removeFromWishlist(PDO $pdo, int $idLivre): void
{
    startSession();
    $idLecteur = (int)($_SESSION['user_id'] ?? 0);
    if ($idLecteur <= 0) {
        return;
    }

    $stmt = $pdo->prepare(
        'DELETE FROM liste_lecture
         WHERE id_livre = :id_livre AND id_lecteur = :id_lecteur'
    );
    $stmt->execute(['id_livre' => $idLivre, 'id_lecteur' => $idLecteur]);
}
