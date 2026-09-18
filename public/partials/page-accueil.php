<section class="hero">
    <h1>Découvrez votre prochaine lecture</h1>
    <p>Recherchez un livre par son titre ou son auteur parmi notre collection ouverte.</p>

    <form class="search-form" action="index.php" method="get">
        <input type="hidden" name="page" value="recherche">
        <div class="search-form__field">
             <input type="text" name="q" placeholder="Titre, auteur, mot-clé…" autocomplete="off">
        </div>
        <button type="submit" class="btn btn-primary">Rechercher →</button>
    </form>

    <p class="search-suggestions">
        Suggestions :
        <a href="index.php?page=recherche&q=Mariama+BA">Mariama BA</a>
        <a href="index.php?page=recherche&q=Ahmadou+Kourouma">Ahmadou Kourouma</a>
    </p>
</section>

<section class="section">
    <div class="section__header">
        <h2>Quelques livres à découvrir</h2>
        <a class="section__link" href="index.php?page=recherche">Parcourir tout le fonds →</a>
    </div>
    <p class="section__subtitle">Sélection intemporelle pour cultiver la curiosité et la réflexion sereine.</p>

    <div class="book-grid">
        <?php foreach ($livresSuggestions as $livre): ?>
            <?php include __DIR__ . '/book-card.php'; ?>
        <?php endforeach; ?>

        <?php if (empty($livresSuggestions)): ?>
            <p class="empty-state">Aucun livre dans le catalogue pour le moment. <a href="create.php">Ajoutez-en un</a>.</p>
        <?php endif; ?>
    </div>
</section>
