<div class="results-bar">
    <form class="search-form" action="index.php" method="get" style="margin:0; max-width:none;">
        <input type="hidden" name="page" value="recherche">
        <div class="search-form__field">
             <input type="text" name="q" value="<?= h($termeRecherche) ?>" placeholder="Titre, auteur, mot-clé…" autocomplete="off">
        </div>
        <button type="submit" class="btn btn-primary">Rechercher</button>
    </form>
</div>

<p class="results-count">
    <?= count($resultats) ?> livre<?= count($resultats) > 1 ? 's' : '' ?> trouvé<?= count($resultats) > 1 ? 's' : '' ?>
    <?php if ($termeRecherche !== ''): ?>
        pour votre recherche « <strong><?= h($termeRecherche) ?></strong> »
    <?php endif; ?>
</p>

<div class="book-grid">
    <?php foreach ($resultats as $livre): ?>
        <?php include __DIR__ . '/book-card.php'; ?>
    <?php endforeach; ?>
</div>

<?php if (empty($resultats)): ?>
    <div class="empty-state">
        <h2>Aucun résultat</h2>
        <p>Essayez avec un autre titre ou le nom d'un auteur.</p>
    </div>
<?php endif; ?>
