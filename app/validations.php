<?php

//Validation des données soumises via les formulaires (ajout / modification de livre).


function validerLivre(array $data): array
{
    $erreurs = [];

    $titre = trim($data['titre'] ?? '');
    $auteur = trim($data['auteur'] ?? '');
    $description = trim($data['description'] ?? '');
    $maisonEdition = trim($data['maison_edition'] ?? '');
    $nombreExemplaire = $data['nombre_exemplaire'] ?? '';

    if ($titre === '') {
        $erreurs['titre'] = 'Le titre est obligatoire.';
    } elseif (mb_strlen($titre) > 100) {
        $erreurs['titre'] = 'Le titre ne doit pas dépasser 100 caractères.';
    }

    if ($auteur === '') {
        $erreurs['auteur'] = 'L\'auteur est obligatoire.';
    } elseif (mb_strlen($auteur) > 100) {
        $erreurs['auteur'] = 'Le nom de l\'auteur ne doit pas dépasser 100 caractères.';
    }
    if ($description === '') {
        $erreurs['description'] = 'La description est obligatoire.';
    } elseif (mb_strlen($description) > 1000) {
        $erreurs['description'] = 'La description ne doit pas dépasser 1000 caractères.';
    }

    if (mb_strlen($maisonEdition) > 100) {
        $erreurs['maison_edition'] = 'La maison d\'édition ne doit pas dépasser 100 caractères.';
    }

    if ($nombreExemplaire === '' || filter_var($nombreExemplaire, FILTER_VALIDATE_INT) === false) {
        $erreurs['nombre_exemplaire'] = 'Le nombre d\'exemplaires doit être un nombre entier.';
    } elseif ((int)$nombreExemplaire < 0) {
        $erreurs['nombre_exemplaire'] = 'Le nombre d\'exemplaires ne peut pas être négatif.';
    }

    return $erreurs;
}
