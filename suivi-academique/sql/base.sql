-- ============================================================
--  BASE DE DONNÉES : prev_echec_technolab
--  Projet : Suivi Académique – TechnoLAB-ISTA
--  Groupe II – L2 IG | Année 2024-2025
-- ============================================================

CREATE DATABASE IF NOT EXISTS prev_echec_technolab
    CHARACTER SET utf8
    COLLATE utf8_general_ci;

USE prev_echec_technolab;

-- ────────────────────────────────────────────────────────────
-- TABLE 1 : Filières
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS Filieres (
    id_filiere  INT AUTO_INCREMENT PRIMARY KEY,
    nom_filiere VARCHAR(100) NOT NULL,
    niveau      VARCHAR(20)  NOT NULL
);

-- ────────────────────────────────────────────────────────────
-- TABLE 2 : Étudiants (Module Scanner)
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS Etudiants (
    matricule  VARCHAR(50)  PRIMARY KEY,
    nom        VARCHAR(50)  NOT NULL,
    prenom     VARCHAR(50)  NOT NULL,
    email      VARCHAR(100) UNIQUE,
    id_filiere INT,
    FOREIGN KEY (id_filiere) REFERENCES Filieres(id_filiere)
        ON DELETE SET NULL
);

-- ────────────────────────────────────────────────────────────
-- TABLE 3 : Matières
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS Matieres (
    id_matiere  INT AUTO_INCREMENT PRIMARY KEY,
    nom_matiere VARCHAR(100) NOT NULL,
    coefficient INT          DEFAULT 1
);

-- ────────────────────────────────────────────────────────────
-- TABLE 4 : Notes (Module Cœur – Performances)
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS Notes (
    id_note            INT AUTO_INCREMENT PRIMARY KEY,
    matricule_etudiant VARCHAR(50),
    id_matiere         INT,
    note_devoir        DECIMAL(4,2),
    note_examen        DECIMAL(4,2),
    semestre           INT NOT NULL,
    FOREIGN KEY (matricule_etudiant) REFERENCES Etudiants(matricule)
        ON DELETE CASCADE,
    FOREIGN KEY (id_matiere) REFERENCES Matieres(id_matiere)
        ON DELETE CASCADE
);

-- ────────────────────────────────────────────────────────────
-- TABLE 5 : Absences (Module Cœur – Assiduité)
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS Absences (
    id_absence         INT AUTO_INCREMENT PRIMARY KEY,
    matricule_etudiant VARCHAR(50),
    id_matiere         INT,
    date_absence       DATE NOT NULL,
    nombre_heures      INT  DEFAULT 2,
    FOREIGN KEY (matricule_etudiant) REFERENCES Etudiants(matricule)
        ON DELETE CASCADE,
    FOREIGN KEY (id_matiere) REFERENCES Matieres(id_matiere)
        ON DELETE CASCADE
);

-- ────────────────────────────────────────────────────────────
-- TABLE 6 : Diagnostics et Alertes (Module Cerveau)
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS Diagnostics (
    id_diagnostic      INT AUTO_INCREMENT PRIMARY KEY,
    matricule_etudiant VARCHAR(50),
    moyenne_generale   DECIMAL(4,2),
    total_absences     INT          DEFAULT 0,
    statut_zone        VARCHAR(20)  NOT NULL,  -- Zone verte / Zone orange / Zone rouge
    date_analyse       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (matricule_etudiant) REFERENCES Etudiants(matricule)
        ON DELETE CASCADE
);

-- ────────────────────────────────────────────────────────────
-- TABLE 7 : Utilisateurs (Authentification + Rôles)
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    nom          VARCHAR(100) NOT NULL,
    email        VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role         ENUM('admin', 'enseignant', 'etudiant') NOT NULL DEFAULT 'etudiant'
);

-- ============================================================
--  DONNÉES DE TEST
-- ============================================================

-- ── Comptes utilisateurs ─────────────────────────────────────
INSERT INTO users (nom, email, mot_de_passe, role) VALUES
('Administrateur',   'admin@technolab.ml',    'admin123',    'admin'),
('Sadio Sall',       'sadio@technolab.ml',     'prof123',     'enseignant'),
('Mamadou Diallo',   'mamadou@technolab.ml',   'etudiant123', 'etudiant');

-- ── Filières ─────────────────────────────────────────────────
INSERT INTO Filieres (nom_filiere, niveau) VALUES
('Informatique de Gestion', 'L2'),
('Réseaux et Télécoms',     'L2'),
('Gestion Commerciale',     'L1');

-- ── Matières ─────────────────────────────────────────────────
INSERT INTO Matieres (nom_matiere, coefficient) VALUES
('UML et Modélisation',      3),
('Mathématiques Appliquées', 2),
('Base de données',          3),
('Anglais Professionnel',    2),
('Technologies Web (TEC)',   3);

-- ── Étudiants ────────────────────────────────────────────────
INSERT INTO Etudiants (matricule, nom, prenom, email, id_filiere) VALUES
('ISTA222222',   'TALL',   'Fifi',   'fifi@technolab.ml',   1),
('ISTAAA3444',   'BARRY',  'Daouda', 'daouda@technolab.ml', 1),
('ISTA237499',   'SOW',    'Binta',  'binta@technolab.ml',  1),
('ISTA738237',   'BERTHE', 'Fah',    'fah@technolab.ml',    1),
('ISTA2832737',  'LAH',    'Mariam', 'mariam@technolab.ml', 1);

-- ── Notes ────────────────────────────────────────────────────
INSERT INTO Notes (matricule_etudiant, id_matiere, note_devoir, note_examen, semestre) VALUES
-- Fifi TALL
('ISTA222222', 1, 13.00, 15.00, 1),
('ISTA222222', 2, 14.00, 12.00, 1),
('ISTA222222', 3, 12.00, 14.00, 1),
('ISTA222222', 4, 11.00, 13.00, 1),
('ISTA222222', 5, 15.00, 16.00, 1),
-- Daouda BARRY
('ISTAAA3444', 1, 10.00, 12.00, 1),
('ISTAAA3444', 2, 11.00, 13.00, 1),
('ISTAAA3444', 3, 12.00, 11.00, 1),
('ISTAAA3444', 4, 10.00, 14.00, 1),
('ISTAAA3444', 5, 13.00, 11.00, 1),
-- Binta SOW
('ISTA237499', 1,  8.00, 10.00, 1),
('ISTA237499', 2,  9.00, 11.00, 1),
('ISTA237499', 3, 10.00,  9.00, 1),
('ISTA237499', 4,  8.00, 12.00, 1),
('ISTA237499', 5, 11.00,  9.00, 1),
-- Fah BERTHE
('ISTA738237', 1, 16.00, 18.00, 1),
('ISTA738237', 2, 15.00, 19.00, 1),
('ISTA738237', 3, 17.00, 16.00, 1),
('ISTA738237', 4, 14.00, 18.00, 1),
('ISTA738237', 5, 18.00, 17.00, 1),
-- Mariam LAH
('ISTA2832737', 1,  5.00,  7.00, 1),
('ISTA2832737', 2,  6.00,  8.00, 1),
('ISTA2832737', 3,  4.00,  6.00, 1),
('ISTA2832737', 4,  7.00,  9.00, 1),
('ISTA2832737', 5,  5.00,  8.00, 1);

-- ── Absences ─────────────────────────────────────────────────
INSERT INTO Absences (matricule_etudiant, id_matiere, date_absence, nombre_heures) VALUES
('ISTA237499',  1, '2025-03-10', 2),
('ISTA237499',  2, '2025-03-15', 2),
('ISTA237499',  3, '2025-03-20', 4),
('ISTA237499',  4, '2025-04-01', 2),
('ISTA2832737', 1, '2025-02-20', 2),
('ISTA2832737', 2, '2025-02-25', 2),
('ISTA2832737', 3, '2025-03-01', 4),
('ISTA2832737', 4, '2025-03-10', 2),
('ISTA2832737', 5, '2025-03-15', 2),
('ISTA2832737', 1, '2025-03-22', 2),
('ISTAAA3444',  2, '2025-03-18', 2);

-- ── Diagnostics (résultats du Module Cerveau) ────────────────
INSERT INTO Diagnostics (matricule_etudiant, moyenne_generale, total_absences, statut_zone) VALUES
('ISTA222222',  14.00, 0,  'Zone verte'),
('ISTAAA3444',  12.00, 2,  'Zone verte'),
('ISTA237499',  10.00, 10, 'Zone orange'),
('ISTA738237',  17.00, 0,  'Zone verte'),
('ISTA2832737',  6.78, 14, 'Zone rouge');
