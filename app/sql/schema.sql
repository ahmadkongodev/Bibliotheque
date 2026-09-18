-- =========================================================
-- Structure de la base de données 
-- =========================================================

-- Force l'encodage UTF-8 côté client pour éviter tout problème d'accents
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS bibliotheque
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE bibliotheque;

DROP TABLE IF EXISTS liste_lecture;
DROP TABLE IF EXISTS livres;
DROP TABLE IF EXISTS lecteurs;

-- ---------------------------------------------------------
-- Table : livres
-- ---------------------------------------------------------
CREATE TABLE livres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    auteur VARCHAR(100) NOT NULL,
    description TEXT,
    maison_edition VARCHAR(100),
    nombre_exemplaire INT DEFAULT 0,
    couverture VARCHAR(255) DEFAULT NULL,
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Table : lecteurs
-- ---------------------------------------------------------
CREATE TABLE lecteurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Table : liste_lecture (table d'association)
-- ---------------------------------------------------------
CREATE TABLE liste_lecture (
    id_livre INT NOT NULL,
    id_lecteur INT NOT NULL,
    date_emprunt DATE DEFAULT NULL,
    date_retour DATE DEFAULT NULL,
    PRIMARY KEY (id_livre, id_lecteur),
    FOREIGN KEY (id_livre) REFERENCES livres(id) ON DELETE CASCADE,
    FOREIGN KEY (id_lecteur) REFERENCES lecteurs(id) ON DELETE CASCADE
) ENGINE=InnoDB;
