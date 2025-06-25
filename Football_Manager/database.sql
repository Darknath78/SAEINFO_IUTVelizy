-- #############################################################################
-- #                                                                           #
-- #         SCRIPT DE CRÉATION ET DE REMPLISSAGE DE LA BASE DE DONNÉES         #
-- #                        Projet SAE23 - Football_Manager                     #
-- #                                                                           #
-- #############################################################################


-- -----------------------------------------------------------------------------
-- ÉTAPE 1 : CRÉATION DE LA STRUCTURE DE LA BASE DE DONNÉES
-- -----------------------------------------------------------------------------

-- Suppression de la base si elle existe pour éviter les erreurs
DROP DATABASE IF EXISTS sae23_foot;

-- Création de la base de données avec encodage UTF8
CREATE DATABASE sae23_foot CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Sélection de la base de données pour les commandes suivantes
USE sae23_foot;


-- CRÉATION DES TABLES

-- Table `joueurs`
-- Stocke les informations sur chaque joueur de l'équipe.
CREATE TABLE joueurs (
                         id_joueur INT PRIMARY KEY AUTO_INCREMENT,
                         nom VARCHAR(50) NOT NULL,
                         prenom VARCHAR(50) NOT NULL,
                         poste VARCHAR(50),
    -- Contrainte CHECK pour valider le poste parmi une liste prédéfinie.
                         CONSTRAINT chk_poste CHECK (poste IN ('Gardien', 'Défenseur', 'Milieu', 'Attaquant')),
                         numero_maillot INT UNIQUE,
    -- Contrainte CHECK pour s'assurer que le numéro de maillot est positif.
                         CONSTRAINT chk_numero_maillot CHECK (numero_maillot > 0 AND numero_maillot < 100)
);

-- Table `utilisateurs`
-- Gère les comptes de connexion à l'application.
CREATE TABLE utilisateurs (
                              id_utilisateur INT PRIMARY KEY AUTO_INCREMENT,
                              login VARCHAR(50) NOT NULL UNIQUE,
                              mot_de_passe VARCHAR(255) NOT NULL, -- Assez long pour les mots de passe hashés
                              role ENUM('manager', 'joueur') NOT NULL,
                              id_joueur_associe INT,
                              FOREIGN KEY (id_joueur_associe) REFERENCES joueurs(id_joueur) ON DELETE SET NULL
);

-- Table `matchs`
-- Contient les informations sur les matchs planifiés ou joués.
CREATE TABLE matchs (
                        id_match INT PRIMARY KEY AUTO_INCREMENT,
                        adversaire VARCHAR(100) NOT NULL,
                        photo_adversaire VARCHAR(255) DEFAULT 'default_logo.png',
                        date_match DATETIME NOT NULL,
                        lieu VARCHAR(100),
                        score_equipe INT DEFAULT NULL,
                        score_adversaire INT DEFAULT NULL,
                        tournoi VARCHAR(100),
    -- Contrainte CHECK pour s'assurer que les scores ne sont pas négatifs.
                        CONSTRAINT chk_scores CHECK (score_equipe >= 0 AND score_adversaire >= 0)
);

-- Table `compositions`
-- Table de liaison pour définir quel joueur a joué à quel match et à quel poste.
CREATE TABLE compositions (
                              id_match INT,
                              id_joueur INT,
                              position_terrain VARCHAR(50),
    -- Clé primaire composite pour garantir qu'un joueur ne peut pas être deux fois dans la même compo.
                              PRIMARY KEY (id_match, id_joueur),
                              FOREIGN KEY (id_match) REFERENCES matchs(id_match) ON DELETE CASCADE,
                              FOREIGN KEY (id_joueur) REFERENCES joueurs(id_joueur) ON DELETE CASCADE
);


-- -----------------------------------------------------------------------------
-- ÉTAPE 2 : REMPLISSAGE DES TABLES AVEC DES DONNÉES DE TEST
-- -----------------------------------------------------------------------------

-- Remplissage de la table `joueurs` (11 joueurs)
INSERT INTO joueurs (nom, prenom, poste, numero_maillot) VALUES
                                                             ('Lloris', 'Hugo', 'Gardien', 1),
                                                             ('Pavard', 'Benjamin', 'Défenseur', 2),
                                                             ('Koundé', 'Jules', 'Défenseur', 4),
                                                             ('Upamecano', 'Dayot', 'Défenseur', 5),
                                                             ('Hernandez', 'Théo', 'Défenseur', 22),
                                                             ('Tchouaméni', 'Aurélien', 'Milieu', 8),
                                                             ('Rabiot', 'Adrien', 'Milieu', 14),
                                                             ('Griezmann', 'Antoine', 'Milieu', 7),
                                                             ('Dembélé', 'Ousmane', 'Attaquant', 11),
                                                             ('Mbappé', 'Kylian', 'Attaquant', 10),
                                                             ('Giroud', 'Olivier', 'Attaquant', 9);

-- Remplissage de la table `utilisateurs`
-- Mots de passe hashés : 'manager_pass' pour le manager, 'joueur_pass' pour le joueur.
INSERT INTO utilisateurs (login, mot_de_passe, role, id_joueur_associe) VALUES
                                                                            ('manager', '$2y$10$5Grw4//x8iNZkfVp9.M/o.hrErokYkU/d8j0BmV5uHDIm4PjFlkBe', 'manager', NULL),
                                                                            ('kmbappe', '$2y$10$LS9ubt2.Va0K2SQ.5AGsFuNO1r4i3pDMNVe02liQLrMTK8elw4jTy', 'joueur', 10),

-- Remplissage de la table `matchs` (10 matchs)
INSERT INTO matchs (adversaire, date_match, lieu, score_equipe, score_adversaire, tournoi) VALUES
                                                                                               ('Argentine', '2024-12-18 20:00:00', 'Lusail Stadium', 3, 3, 'Coupe du Monde'),
                                                                                               ('Maroc', '2024-12-14 20:00:00', 'Al Bayt Stadium', 2, 0, 'Coupe du Monde'),
                                                                                               ('Angleterre', '2024-12-10 20:00:00', 'Al Bayt Stadium', 2, 1, 'Coupe du Monde'),
                                                                                               ('Pologne', '2024-12-04 16:00:00', 'Al Thumama Stadium', 3, 1, 'Coupe du Monde'),
                                                                                               ('Tunisie', '2024-11-30 16:00:00', 'Education City Stadium', 0, 1, 'Coupe du Monde'),
                                                                                               ('Danemark', '2024-11-26 17:00:00', 'Stadium 974', 2, 1, 'Coupe du Monde'),
                                                                                               ('Australie', '2024-11-22 20:00:00', 'Al Janoub Stadium', 4, 1, 'Coupe du Monde'),
                                                                                               ('Allemagne', '2025-09-05 20:45:00', 'Stade de France', NULL, NULL, 'Ligue des Nations'),
                                                                                               ('Espagne', '2025-09-08 20:45:00', 'Stade de France', NULL, NULL, 'Ligue des Nations'),
                                                                                               ('Portugal', '2025-10-10 20:45:00', 'Estádio da Luz', NULL, NULL, 'Amical');

-- Remplissage de la table `compositions` pour le premier match (vs Argentine)
INSERT INTO compositions (id_match, id_joueur, position_terrain) VALUES
                                                                     (1, 1, 'Gardien'),
                                                                     (1, 2, 'Arrière Droit'),
                                                                     (1, 4, 'Défenseur Central'),
                                                                     (1, 5, 'Défenseur Central'),
                                                                     (1, 22, 'Arrière Gauche'),
                                                                     (1, 8, 'Milieu Défensif'),
                                                                     (1, 14, 'Milieu Relayeur'),
                                                                     (1, 7, 'Milieu Offensif'),
                                                                     (1, 11, 'Ailier Droit'),
                                                                     (1, 10, 'Ailier Gauche'),
                                                                     (1, 9, 'Avant-Centre');


-- -----------------------------------------------------------------------------
-- ÉTAPE 3 : EXEMPLES DE REQUÊTES D'EXPLOITATION (selon le cahier des charges)
-- -----------------------------------------------------------------------------

-- 1. Requête simple : Sélectionner tous les joueurs
SELECT nom, prenom, poste FROM joueurs;

-- 2. Requête avec jointure : Afficher les joueurs et leur position pour un match donné (match ID 1)
SELECT j.prenom, j.nom, c.position_terrain
FROM joueurs j
         JOIN compositions c ON j.id_joueur = c.id_joueur
WHERE c.id_match = 1;

-- 3. Requête avec agrégation (MAX) : Trouver le numéro de maillot le plus élevé
SELECT MAX(numero_maillot) AS 'Numero le plus eleve' FROM joueurs;

-- 4. Requête avec GROUP BY et HAVING : Lister les postes ayant plus de 2 joueurs
SELECT poste, COUNT(id_joueur) AS nombre_de_joueurs
FROM joueurs
GROUP BY poste
HAVING nombre_de_joueurs > 2;

-- 5. Requête imbriquée (sous-requête) : Trouver les joueurs qui n'ont pas encore été placés dans une composition
SELECT nom, prenom
FROM joueurs
WHERE id_joueur NOT IN (SELECT DISTINCT id_joueur FROM compositions);

-- 6. Requête de mise à jour (UPDATE) : Mettre à jour le score du match contre l'Allemagne (match ID 8)
UPDATE matchs
SET score_equipe = 2, score_adversaire = 1
WHERE id_match = 8;

-- 7. Requête de suppression (DELETE) : Supprimer le match amical contre le Portugal (match ID 10)
DELETE FROM matchs WHERE id_match = 10;
