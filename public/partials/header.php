<?php
require_once __DIR__ . '/../../app/auth.php';
$nbWishlist = count(getWishlist($pdo));
?>
<header class="site-header">
    <nav class="navbar">
        <a href="index.php?page=accueil" class="navbar__brand">
            
            Bibliothèque
        </a>
        <ul class="navbar__links">
            <li><a href="index.php?page=accueil" class="<?= $page === 'accueil' ? 'active' : '' ?>">Accueil</a></li>
            <li><a href="index.php?page=recherche" class="<?= $page === 'recherche' ? 'active' : '' ?>">Rechercher</a></li>
            <?php if (is_logged_in()): ?>
                <li><a href="index.php?page=liste" class="<?= $page === 'liste' ? 'active' : '' ?>">Ma liste <?php if ($nbWishlist > 0): ?><span class="navbar__badge"><?= $nbWishlist ?></span><?php endif; ?></a></li>
                <li><a href="create.php">Gérer les livres</a></li>
                <li><a href="logout.php" data-confirm="Voulez-vous vraiment vous déconnecter ?">Déconnexion</a></li>
            <?php else: ?>
                <li><a href="login.php">Connexion</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
