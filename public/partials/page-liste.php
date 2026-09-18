<p class="breadcrumb"><a href="index.php?page=accueil">Accueil</a> &rsaquo; Ma sélection</p>

<h1>Ma liste de lecture</h1>
<p class="section__subtitle">Vous avez <?= count($wishlistLivres) ?> livre<?= count($wishlistLivres) > 1 ? 's' : '' ?> sauvegardé<?= count($wishlistLivres) > 1 ? 's' : '' ?> dans votre panier de lecture.</p>

<?php if (empty($wishlistLivres)): ?>
    <div class="empty-state">
        <h2>Votre liste est vide</h2>
        <p>Parcourez le <a href="index.php?page=recherche">catalogue</a> et ajoutez des livres à votre liste.</p>
    </div>
<?php else: ?>
    <?php foreach ($wishlistLivres as $livre): ?>
        <div class="wishlist-item">
            <img class="wishlist-item__cover" src="<?= h(couvertureUrl($livre['couverture'])) ?>" alt="Couverture de <?= h($livre['titre']) ?>" onerror="this.onerror=null;this.src='<?= h(COUVERTURE_DEFAUT) ?>';">
            <div class="wishlist-item__body">
                <h3 class="book-card__title"><?= h($livre['titre']) ?></h3>
                <p class="book-card__author"><?= h($livre['auteur']) ?></p>
                <p class="book-card__desc"><?= h(tronquer($livre['description'] ?? '', 140)) ?></p>
                <div class="wishlist-item__footer">
                    <a href="index.php?page=details&id=<?= (int)$livre['id'] ?>">Consulter la fiche →</a>
                    <a class="wishlist-item__remove" href="index.php?page=wishlist-remove&id=<?= (int)$livre['id'] ?>">🗑 Retirer</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
