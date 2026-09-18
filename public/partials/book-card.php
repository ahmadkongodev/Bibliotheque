<?php
/**
 * Attend une variable $livre (tableau associatif) dans le scope appelant.
 */
?>
<article class="book-card">
    <img class="book-card__cover" src="<?= h(couvertureUrl($livre['couverture'])) ?>" alt="Couverture de <?= h($livre['titre']) ?>" onerror="this.onerror=null;this.src='<?= h(COUVERTURE_DEFAUT) ?>';">
    <div class="book-card__body">
        <h3 class="book-card__title"><?= h($livre['titre']) ?></h3>
        <p class="book-card__author"><?= h($livre['auteur']) ?></p>
        <?php if (!empty($livre['description'])): ?>
            <p class="book-card__desc"><?= h(tronquer($livre['description'], 90)) ?></p>
        <?php endif; ?>
        <div class="book-card__actions">
            <a class="btn btn-primary btn-sm btn-block" href="index.php?page=details&id=<?= (int)$livre['id'] ?>">Voir les détails</a>
        </div>
    </div>
</article>
