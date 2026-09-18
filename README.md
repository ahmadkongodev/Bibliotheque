# Bibliotheque

Application web PHP/MySQL de gestion et de consultation d'un catalogue de livres.

## Autheur
KONGO Hamado, le 18/09/20266

## Code Source sur github Clonable

- Pour cloner directement le code source du projet dans votre ordinateur, Utilisez la commande
```text 
git clone https://github.com/ahmadkongodev/Bibliotheque.git
```


## 1. Fonctionnalites

- Consultation publique du catalogue.
- Recherche par titre ou auteur.
- Affichage des details d'un livre.
- Liste de lecture persistante en base de donnees.
- Creation de compte lecteur.
- Connexion et deconnexion securisees.
- Ajout, modification et suppression de livres pour les utilisateurs connectes.
- Upload local des couvertures d'images.
- Protection CSRF des formulaires sensibles.

## 2. Prerequis

- Windows avec Laragon (ou un serveur Apache/Nginx equivalent).
- PHP 8 recommande.
- Extensions PHP `pdo_mysql`, `fileinfo`, `mbstring` et `session`.
- MySQL ou MariaDB.

Le projet est configure pour Laragon dans `C:\laragon\www\Bibliotheque`.

## 3. Structure du projet

```text
app/
  auth.php                 Sessions, connexion et protection CSRF
  config.php               Configuration de la base de donnees
  db.php                   Connexion PDO partagee
  helpers.php              Fonctions utilitaires et upload d'images
  validations.php          Validation des livres
  sql/
    schema.sql             Creation de la base et des tables
    donnees.sql            Donnees de demonstration
    auth_migration.sql     Migration pour une base deja existante

public/
  index.php                Point d'entree public et routage du catalogue
  login.php                Connexion
  register.php             Creation de compte
  logout.php               Deconnexion
  create.php               Ajout et administration des livres
  edit.php                 Modification d'un livre
  delete.php               Suppression d'un livre
  assets/uploads/          Couvertures stockees localement
  partials/                Blocs HTML partages
  style/                   Feuilles de style
  js/                      JavaScript d'interface
```

## 4. Installation

### 4.1 Configuration PHP/MySQL

Verifier les valeurs dans `app/config.php` :

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'bibliotheque');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
```

Adapter `DB_USER` et `DB_PASS` si l'installation MySQL utilise d'autres identifiants.

### 4.2 Creation de la base

Dans phpMyAdmin, HeidiSQL ou le client MySQL, executer dans cet ordre :

1. `app/sql/schema.sql`
2. `app/sql/donnees.sql`

Le schema cree la base `bibliotheque`, les tables `livres`, `lecteurs` et `liste_lecture`, puis les donnees de demonstration sont inserees.

Cette colonne est necessaire pour l'inscription et la connexion.

### 4.3 Demarrage

Avec Laragon demarre, ouvrir :

```text
http://localhost/Bibliotheque/public/index.php
```

Selon la configuration Apache, `http://bibliotheque.test` peut aussi fonctionner.

## 5. Compte de demonstration

Le fichier `app/sql/donnees.sql` fournit un compte pret a l'emploi :

```text
Email : test@email.com
Mot de passe : admin123
```

Le mot de passe est stocke sous forme de hash avec `password_hash()`. Ne pas utiliser ce compte en production.

## 6. Parcours utilisateur

### Visiteur

Les pages suivantes restent publiques :

- `index.php?page=accueil`
- `index.php?page=recherche`
- `index.php?page=details&id=ID`
- `register.php`
- `login.php`

La liste de lecture est stockee dans `liste_lecture` et liee au compte connecte par `id_lecteur`. Elle reste donc disponible apres une nouvelle connexion ou depuis un autre navigateur.

### Utilisateur connecte

Les pages suivantes exigent une session valide :

- `index.php?page=liste`
- `create.php`
- `edit.php?id=ID`
- `delete.php` avec une requete POST

La fonction `require_login()` dans `app/auth.php` redirige les visiteurs non connectes vers `login.php`.

## 7. Authentification et securite

- Les mots de passe sont verifies avec `password_verify()`.
- Les mots de passe sont crees avec `password_hash()`.
- L'identifiant de session est regenere apres une connexion reussie.
- Les formulaires de connexion, inscription, ajout, modification et suppression utilisent un jeton CSRF.
- Les requetes SQL utilisent PDO et des requetes preparees.
- Les noms de fichiers uploades sont aleatoires et ne reprennent pas le nom fourni par le navigateur.
- L'upload accepte JPG, PNG, GIF et WebP, avec une taille maximale de 2 Mo.

Avant une mise en production, desactiver l'affichage des erreurs PHP, utiliser HTTPS, remplacer les identifiants MySQL par des secrets securises et supprimer les identifiants de demonstration affiches dans `login.php`.

## 8. Couvertures de livres

Les images sont enregistrees dans :

```text
public/assets/uploads/
```

La base ne contient pas l'image elle-meme : la colonne `livres.couverture` contient un chemin relatif tel que :

```text
assets/uploads/6f2a...jpg
```

Le formulaire affiche un apercu local avant l'envoi. Lors d'une modification, l'ancienne couverture est conservee si aucune nouvelle image n'est choisie. Lors d'un remplacement ou d'une suppression de livre, l'ancien fichier local est supprime.

## 9. Depannage


### Image non visible

Verifier que :

- le fichier est bien dans `public/assets/uploads/` ;
- la valeur de `livres.couverture` commence par `assets/uploads/` ;
- le dossier est lisible par le serveur web ;
- le formulaire contient `enctype="multipart/form-data"`.

### Page de gestion inaccessible

Se connecter via `login.php`. Les pages d'administration appellent `require_login()`.

## 10. Tests rapides

1. Ouvrir le catalogue sans connexion.
2. Se connecter avec le compte de demonstration.
3. Ouvrir `Gerer les livres`.
4. Ajouter un livre avec une image JPG ou PNG.
5. Verifier l'image dans le catalogue et dans la page de modification.
6. Remplacer l'image, puis verifier que l'ancienne est supprimee.
7. Se deconnecter et verifier la confirmation.
8. Tenter d'ouvrir `create.php` sans session et verifier la redirection vers `login.php`.
