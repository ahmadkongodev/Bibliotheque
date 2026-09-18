
-- Force l'encodage UTF-8 côté client pour éviter tout problème d'accents
SET NAMES utf8mb4;

USE bibliotheque;

-- Compte de démonstration utilisé aussi pour la liste de lecture
-- Connexion : test@email.com / admin123
INSERT INTO lecteurs (id, nom, prenom, email, mot_de_passe) VALUES
(1, 'Admin', 'Bibliothèque', 'test@email.com', '$2y$10$wRwLIU/glq.fWBPsw5E.6eYnsLa8EvmNmzgtT/MylikiPLnm0qto2')


-- Livres d'auteurs africains
INSERT INTO livres (titre, auteur, description, maison_edition, nombre_exemplaire, couverture) VALUES
('Une si longue lettre', 'Mariama Bâ', 'Sous forme de lettre, une femme sénégalaise raconte son veuvage et la condition féminine dans une société en mutation.', 'Nouvelles Éditions Africaines', 4, 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1631135443i/47973572.jpg'),
('Les Soleils des indépendances', 'Ahmadou Kourouma', 'Chronique satirique et poétique du déclin d''un prince malinké après les indépendances africaines.', 'Seuil', 3, ''),
('L''Étrange Destin de Wangrin', 'Amadou Hampâté Bâ', 'Le récit picaresque d''un interprète malin qui utilise ses talents pour s''enrichir sous l''administration coloniale.', 'Union Générale d''Éditions', 2, ''),
('Une saison de flammes', 'Nawal El Saadawi', 'Un roman puissant qui explore les rapports de pouvoir, le genre et la révolte dans la société égyptienne.', 'Actes Sud', 3, ''),
('Ainsi parla l''oncle', 'Jean Price-Mars', 'Essai fondateur sur la culture et les traditions haïtiennes d''origine africaine, pilier de la pensée panafricaine.', 'Imprimerie de Compiègne', 2, ''),
('Le Ventre de l''Atlantique', 'Fatou Diome', 'Une jeune Sénégalaise partagée entre son île natale et la France raconte l''exil et le mythe de l''Eldorado européen.', 'Anne Carrière', 5, ''),
('Purple Hibiscus', 'Chimamanda Ngozi Adichie', 'Une adolescente nigériane grandit sous l''autorité rigide d''un père tyrannique, entre foi, silence et émancipation.', 'Farafina', 4, ''),
('Petits Chocs des continents', 'Alain Mabanckou', 'Un recueil d''essais sur l''identité, la migration et le regard croisé entre l''Afrique et l''Occident.', 'Éditions Philippe Rey', 3, ''),
('Aya de Yopougon', 'Marguerite Abouet', 'La vie quotidienne et pleine d''humour d''une jeune femme dans le quartier populaire de Yopougon à Abidjan.', 'Gallimard', 6, ''),
('Things Fall Apart', 'Chinua Achebe', 'L''histoire d''Okonkwo, guerrier igbo, et de la confrontation entre les traditions africaines et la colonisation.', 'Heinemann', 5, '');
