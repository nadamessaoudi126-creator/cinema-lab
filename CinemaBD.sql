-- =====================================================
-- Base de données : CinemaBD
-- TP6 : Sessions & Cookies - Gestion de films
-- =====================================================

CREATE DATABASE IF NOT EXISTS CinemaBD CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE CinemaBD;

-- -----------------------------------------------------
-- Table : films
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS films (
    id          INT(11)        NOT NULL AUTO_INCREMENT,
    titre       VARCHAR(150)   NOT NULL,
    realisateur VARCHAR(100)   NOT NULL,
    annee       INT(4)         NOT NULL,
    genre       VARCHAR(50)    NOT NULL,
    note        DECIMAL(3,1)   NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Données : films
-- -----------------------------------------------------
INSERT INTO films (titre, realisateur, annee, genre, note) VALUES
('The Godfather',                      'Francis Ford Coppola', 1972, 'Crime',           9.2),
('The Dark Knight',                    'Christopher Nolan',    2008, 'Action',           9.0),
('Pulp Fiction',                       'Quentin Tarantino',    1994, 'Crime',            8.9),
('Inception',                          'Christopher Nolan',    2010, 'Science-Fiction',  8.8),
('Interstellar',                       'Christopher Nolan',    2014, 'Science-Fiction',  8.6),
('Parasite',                           'Bong Joon-ho',         2019, 'Thriller',         8.5),
('Intouchables',                       'Olivier Nakache',      2011, 'Comédie',          8.5),
('Le Fabuleux Destin d\'Amélie Poulain','Jean-Pierre Jeunet',  2001, 'Romance',          8.3);

-- -----------------------------------------------------
-- Table : utilisateurs
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS utilisateurs (
    id           INT(11)                     NOT NULL AUTO_INCREMENT,
    login        VARCHAR(60)                 NOT NULL,
    mot_de_passe VARCHAR(255)                NOT NULL,
    role         ENUM('admin','visiteur')    NOT NULL DEFAULT 'visiteur',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Données : utilisateurs
-- mot de passe = cinema2025 (hashé avec password_hash)
-- -----------------------------------------------------
INSERT INTO utilisateurs (login, mot_de_passe, role) VALUES
('admin',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('visiteur', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'visiteur');

-- =====================================================
-- NOTE : Le hash ci-dessus correspond au mot de passe
-- "password" (hash de démonstration Laravel/PHP).
-- Pour utiliser "cinema2025", exécute generer_hash.php
-- et remplace le hash dans cette requête.
-- =====================================================
