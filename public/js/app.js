/**
 * app.js
 * Petites améliorations d'expérience utilisateur côté client.
 * (La logique métier reste côté serveur en PHP — ce fichier ne fait
 * que du confort d'interface, conformément à l'écoconception du site.)
 */

document.addEventListener('DOMContentLoaded', function () {

    // Fait disparaître automatiquement les messages flash après quelques secondes.
    var alertes = document.querySelectorAll('.alert');
    alertes.forEach(function (alerte) {
        setTimeout(function () {
            alerte.style.transition = 'opacity 0.4s ease';
            alerte.style.opacity = '0';
            setTimeout(function () { alerte.remove(); }, 400);
        }, 4000);
    });

    // Focus automatique sur le champ de recherche de la page recherche.
    var champRecherche = document.querySelector('.search-form__field input[name="q"]');
    if (champRecherche && champRecherche.value === '') {
        champRecherche.focus();
    }

    // Confirmation générique pour tout lien marqué data-confirm="...".
    document.querySelectorAll('[data-confirm]').forEach(function (lien) {
        lien.addEventListener('click', function (event) {
            if (!confirm(lien.getAttribute('data-confirm'))) {
                event.preventDefault();
            }
        });
    });

    // Affiche un aperçu de la couverture avant l'envoi du formulaire.
    var champCouverture = document.querySelector('#couverture');
    var apercuCouverture = document.querySelector('#couverture-preview');
    if (champCouverture && apercuCouverture) {
        var imageApercu = apercuCouverture.querySelector('img');
        var texteApercu = apercuCouverture.querySelector('span');
        var urlApercu = null;

        champCouverture.addEventListener('change', function () {
            var fichier = champCouverture.files[0];
            var typesAutorises = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

            if (!fichier) {
                apercuCouverture.hidden = true;
                return;
            }

            if (!typesAutorises.includes(fichier.type) || fichier.size > 2 * 1024 * 1024) {
                champCouverture.value = '';
                apercuCouverture.hidden = true;
                alert('Choisissez une image JPG, PNG, GIF ou WebP de 2 Mo maximum.');
                return;
            }

            if (urlApercu) {
                URL.revokeObjectURL(urlApercu);
            }
            urlApercu = URL.createObjectURL(fichier);
            imageApercu.src = urlApercu;
            texteApercu.textContent = fichier.name;
            apercuCouverture.hidden = false;
        });
    }

});
