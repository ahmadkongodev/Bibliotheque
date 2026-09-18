<?php $livre = $livreDetail; $dejaDansListe = is_logged_in() && isInWishlist($pdo, (int)$livre['id']); ?>

<p class="breadcrumb"><a href="index.php?page=recherche">&larr; Retour aux résultats</a></p>

<div class="book-detail">
    <div class="book-detail__cover">
        <img src="<?= h(couvertureUrl($livre['couverture'])) ?>" alt="Couverture de <?= h($livre['titre']) ?>" onerror="this.onerror=null;this.src='<?= h(COUVERTURE_DEFAUT) ?>';">
    </div>

    <div class="book-detail__info">
        <span class="book-detail__category">Livre</span>
        <h1 class="book-detail__title"><?= h($livre['titre']) ?></h1>
        <p class="book-detail__author">par <?= h($livre['auteur']) ?></p>

        <div class="book-detail__meta">
            <?php if (!empty($livre['maison_edition'])): ?>
                <span><?= h($livre['maison_edition']) ?></span>
            <?php endif; ?>
        </div>

        <div class="book-detail__section">
            <h3>Description</h3>
            <p><?= nl2br(h($livre['description'] ?: 'Aucune description disponible pour ce livre.')) ?></p>
        </div>

        <div class="book-detail__availability">
            <?php if ((int)$livre['nombre_exemplaire'] > 0): ?>
                <span> Disponible à l'emprunt (<?= (int)$livre['nombre_exemplaire'] ?> exemplaire<?= $livre['nombre_exemplaire'] > 1 ? 's' : '' ?> en rayon)</span>
            <?php else: ?>
                <span>Actuellement indisponible</span>
            <?php endif; ?>
        </div>

        <div class="book-detail__actions">
            <?php if ($dejaDansListe): ?>
                <a class="btn btn-secondary" href="index.php?page=wishlist-remove&id=<?= (int)$livre['id'] ?>">retirer de ma liste</a>
            <?php else: ?>
                <a class="btn btn-primary" href="index.php?page=wishlist-add&id=<?= (int)$livre['id'] ?>&retour=<?= urlencode('index.php?page=details&id=' . $livre['id']) ?>"> Ajouter à ma liste de lecture</a>
            <?php endif; ?>
            <a class="btn btn-secondary" href="edit.php?id=<?= (int)$livre['id'] ?>">Modifier la fiche</a>
        </div>
    </div>
</div>
