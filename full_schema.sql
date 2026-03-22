-- Full schema for gestion_pharmacie DB - Fixed structure matching all queries
-- Run: mysql -u root -p gestion_pharmacie < full_schema.sql

DROP DATABASE IF EXISTS gestion_pharmacie;
CREATE DATABASE gestion_pharmacie CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestion_pharmacie;

-- Table: users
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    user_role ENUM('admin', 'staff') DEFAULT 'staff',
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert admin user
INSERT INTO users (username, email, password, full_name, user_role) VALUES
('admin', 'admin@pharmacie.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrateur', 'admin')
ON DUPLICATE KEY UPDATE full_name = 'Administrateur';

-- Table: medicaments (EXACT match for queries: quantite, seuil_minimum, date_peremption)
CREATE TABLE medicaments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(200) NOT NULL,
    matiere_active VARCHAR(255),
    description TEXT,
    code VARCHAR(50) UNIQUE,
    quantite INT DEFAULT 0,
    seuil_minimum INT DEFAULT 10,
    prix_achat DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    prix_vente DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    date_peremption DATE,
    categorie VARCHAR(100),
    fournisseur_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (nom),
    INDEX idx_category (categorie),
    INDEX idx_stock (quantite),
    INDEX idx_expiry (date_peremption)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample medicaments
INSERT INTO medicaments (nom, description, code, quantite, seuil_minimum, prix_achat, prix_vente, date_peremption, categorie) VALUES
('Paracétamol 500mg', 'Antalgique', 'PARA500', 150, 20, 0.50, 1.00, '2025-06-01', 'Antalgiques'),
('Amoxicilline 500mg', 'Antibiotique', 'AMOX500', 80, 15, 2.00, 4.50, '2025-03-15', 'Antibiotiques'),
('Ibuprofène 400mg', 'Anti-inflammatoire', 'IBU400', 5, 10, 0.80, 1.50, '2025-01-20', 'Anti-inflammatoires');

-- Table: clients
CREATE TABLE clients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100),
    telephone VARCHAR(50),
    email VARCHAR(100),
    adresse TEXT,
    points_fidelite INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nom_client (nom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO clients (nom, prenom, telephone, email) VALUES
('Dupont', 'Jean', '0123456789', 'jean.dupont@email.com');

-- Table: fournisseurs
CREATE TABLE fournisseurs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(200) NOT NULL,
    contact VARCHAR(100),
    telephone VARCHAR(50),
    email VARCHAR(100),
    adresse TEXT,
    nif VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nom_fournisseur (nom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: ventes
CREATE TABLE ventes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    numero_facture VARCHAR(50) NOT NULL UNIQUE,
    client_id INT NULL,
    utilisateur_id INT NOT NULL,
    total_ht DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_tva DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_ttc DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    remise DECIMAL(10,2) DEFAULT 0.00,
    total_final DECIMAL(10,2) NOT NULL,
    montant_paye DECIMAL(10,2) NOT NULL,
    monnaie_rendue DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    mode_paiement ENUM('especes', 'carte', 'cheque', 'virement') DEFAULT 'especes',
    statut ENUM('attente', 'paye', 'annule') DEFAULT 'paye',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL,
    INDEX idx_date_vente (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: details_vente
CREATE TABLE details_vente (
    id INT PRIMARY KEY AUTO_INCREMENT,
    vente_id INT NOT NULL,
    medicament_id INT NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    total_ligne DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (vente_id) REFERENCES ventes(id) ON DELETE CASCADE,
    INDEX idx_vente (vente_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: mouvements_stock
CREATE TABLE mouvements_stock (
    id INT PRIMARY KEY AUTO_INCREMENT,
    medicament_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    type_mouvement ENUM('entree', 'sortie', 'ajustement', 'retour') NOT NULL,
    quantite INT NOT NULL,
    motif VARCHAR(255),
    reference_document VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (medicament_id) REFERENCES medicaments(id),
    INDEX idx_date_mouvement (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Additional tables for completeness
CREATE TABLE prescriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    docteur_nom VARCHAR(100),
    patient_nom VARCHAR(100),
    description TEXT,
    fichier_path VARCHAR(255),
    statut ENUM('attente','prepare','termine') DEFAULT 'attente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE reservations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    client_id INT,
    medicament_id INT,
    quantite INT,
    statut ENUM('reserve','recupere','annule') DEFAULT 'reserve',
    date_recuperation_prevue DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (medicament_id) REFERENCES medicaments(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Success message
SELECT 'Database schema created successfully! Tables: users, medicaments, clients, fournisseurs, ventes, details_vente, mouvements_stock, prescriptions, reservations' as message;

