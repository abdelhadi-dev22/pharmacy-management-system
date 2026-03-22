<?php
// setup.php - Page d'installation initiale
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration - PharmaGest</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="login-page">
    <div class="login-container" style="max-width: 800px;">
        <div class="login-card">
            <div class="login-header">
                <h1><i class="fas fa-cogs"></i> Configuration PharmaGest</h1>
                <p>Première installation du système</p>
            </div>
            
            <div style="margin: 20px 0;">
                <h3><i class="fas fa-database"></i> Étape 1 : Créer la base de données</h3>
                <p>Ouvrez phpMyAdmin et exécutez ce script SQL :</p>
                <textarea id="sqlScript" style="width: 100%; height: 300px; font-family: monospace; padding: 10px;" readonly>
-- Création de la base de données
CREATE DATABASE IF NOT EXISTS gestion_pharmacie;
USE gestion_pharmacie;

-- Table des utilisateurs
CREATE TABLE utilisateurs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('admin', 'caissier', 'pharmacien') DEFAULT 'caissier',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Insérer l'administrateur par défaut
-- Email: admin@pharmacie.com | Mot de passe: admin123
INSERT INTO utilisateurs (nom, email, mot_de_passe, role) 
VALUES (
    'Administrateur', 
    'admin@pharmacie.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
    'admin'
);

-- Créer les autres tables si nécessaire...
-- (Les autres tables seront créées automatiquement lors de leur première utilisation)
                </textarea>
                <button onclick="copySQL()" class="btn btn-primary" style="margin-top: 10px;">
                    <i class="fas fa-copy"></i> Copier le script SQL
                </button>
            </div>
            
            <div style="margin: 20px 0;">
                <h3><i class="fas fa-file-code"></i> Étape 2 : Vérifier la configuration</h3>
                <p>Ouvrez le fichier <code>includes/db.php</code> et vérifiez :</p>
                <pre style="background: #f5f5f5; padding: 15px; border-radius: 5px;">
define('DB_HOST', 'localhost');      // Hôte MySQL
define('DB_USER', 'root');           // Utilisateur MySQL
define('DB_PASS', '');              // Mot de passe MySQL
define('DB_NAME', 'gestion_pharmacie'); // Nom de la base</pre>
            </div>
            
            <div style="margin: 20px 0;">
                <h3><i class="fas fa-user-check"></i> Étape 3 : Tester la connexion</h3>
                <p>Identifiants créés automatiquement :</p>
                <div style="background: #e8f4fd; padding: 15px; border-radius: 5px;">
                    <p><strong>Email :</strong> <code>admin@pharmacie.com</code></p>
                    <p><strong>Mot de passe :</strong> <code>admin123</code></p>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="login.php" class="btn btn-success" style="padding: 12px 30px;">
                    <i class="fas fa-check-circle"></i> Aller à la page de connexion
                </a>
                <a href="debug_db.php" class="btn btn-info" style="margin-left: 10px;">
                    <i class="fas fa-bug"></i> Tester la connexion
                </a>
            </div>
        </div>
    </div>
    
    <script>
        function copySQL() {
            const sqlTextarea = document.getElementById('sqlScript');
            sqlTextarea.select();
            document.execCommand('copy');
            alert('Script SQL copié dans le presse-papier !');
        }
    </script>
</body>
</html>