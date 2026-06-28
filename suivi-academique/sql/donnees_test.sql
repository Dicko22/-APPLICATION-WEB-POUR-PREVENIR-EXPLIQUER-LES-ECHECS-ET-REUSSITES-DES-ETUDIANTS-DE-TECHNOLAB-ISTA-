-- ============================================================
-- TABLE UTILISATEURS (à ajouter à base.sql)
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    nom          VARCHAR(100) NOT NULL,
    email        VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role         ENUM('admin','enseignant','etudiant') NOT NULL DEFAULT 'etudiant'
);

-- ─── Comptes de test ────────────────────────────────────────
INSERT INTO users (nom, email, mot_de_passe, role) VALUES
('Administrateur',       'admin@technolab.ml',      'admin123',    'admin'),
('Sadio Sall',           'sadio@technolab.ml',       'prof123',     'enseignant'),
('Mamadou Diallo',       'mamadou@technolab.ml',     'etudiant123', 'etudiant');

-- ─── Données de test : filières ─────────────────────────────
INSERT INTO Filieres (nom_filiere, niveau) VALUES
('Informatique de Gestion', 'L2'),
('Réseaux et Télécoms',     'L2'),
('Gestion Commerciale',     'L1');

-- ─── Données de test : matières ─────────────────────────────
INSERT INTO Matieres (nom_matiere, coefficient) VALUES
('UML et Modélisation',     3),
('Mathématiques Appliquées',2),
('Base de données',         3),
('Anglais Professionnel',   2),
('Technologies Web (TEC)',  3);

-- ─── Données de test : étudiants ────────────────────────────
INSERT INTO Etudiants (matricule, nom, prenom, email, id_filiere) VALUES
('ISTA222222',  'TALL',  'Fifi',   'fifi@technolab.ml',   1),
('ISTAAA3444',  'BARRY', 'Daouda', 'daouda@technolab.ml', 1),
('ISTA237499',  'SOW',   'Binta',  'binta@technolab.ml',  1),
('ISTA738237',  'BERTHE','Fah',    'fah@technolab.ml',    1),
('ISTA2832737', 'LAH',   'Mariam', 'mariam@technolab.ml', 1);

-- ─── Données de test : notes ────────────────────────────────
INSERT INTO Notes (matricule_etudiant, id_matiere, note_devoir, note_examen, semestre) VALUES
('ISTA222222',  1, 13, 15, 1), ('ISTA222222',  2, 14, 12, 1),
('ISTA222222',  3, 12, 14, 1), ('ISTA222222',  4, 11, 13, 1),
('ISTAAA3444',  1, 10, 12, 1), ('ISTAAA3444',  2, 11, 13, 1),
('ISTA237499',  1, 8,  10, 1), ('ISTA237499',  2, 9,  11, 1),
('ISTA738237',  1, 16, 18, 1), ('ISTA738237',  2, 15, 19, 1),
('ISTA2832737', 1, 5,  7,  1), ('ISTA2832737', 2, 6,  8,  1);

-- ─── Données de test : diagnostics ──────────────────────────
INSERT INTO Diagnostics (matricule_etudiant, moyenne_generale, total_absences, statut_zone) VALUES
('ISTA222222',  14.00, 2,  'Zone verte'),
('ISTAAA3444',  12.00, 5,  'Zone verte'),
('ISTA237499',  10.00, 8,  'Zone orange'),
('ISTA738237',  17.00, 1,  'Zone verte'),
('ISTA2832737',  6.78, 15, 'Zone rouge');
