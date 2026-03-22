<?php
// install.php - Installation automatique du système
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation - PharmaGest</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #3498db 0%, #2c3e50 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px;
        }
        .step {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            background: #f8f9fa;
        }
        .step h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
        }
        .btn-primary {
            background: #3498db;
            color: white;
        }
        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        .btn-success {
            background: #27ae60;
            color: white;
        }
        .code-block {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 15px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 14px;
            margin: 15px 0;
            overflow-x: auto;
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #dff0d8;
            border: 1px solid #d6e9c6;
            color: #3c763d;
        }
        .alert-error {
            background: #fee;
            border: 1px solid #fcc;
            color: #e74c3c;
        }
        .footer {
            text-align: center;
            padding: 20px;
            border-top: 1px solid #eee;
            color: #95a5a6;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-cogs"></i> Installation PharmaGest</h1>
            <p>Configuration automatique du système</p>
        </div>
        
        <div class="content">
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $host = $_POST['host'] ?? 'localhost';
                $user = $_POST['user'] ?? 'root';
                $pass = $_POST['pass'] ?? '';
                $dbname = $_POST['dbname'] ?? 'pharma_system';
                
                try {
                    // Connexion à MySQL
                    $pdo = new PDO("mysql:host=$host", $user, $pass);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    echo '<div class="alert alert-success">';
                    echo '<i class="fas fa-check-circle"></i> Connexion MySQL réussie';
                    echo '</div>';
                    
                    // Créer la base de données
                    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    $pdo->exec("USE $dbname");
                    
                    echo '<div class="alert alert-success">';
                    echo '<i class="fas fa-check-circle"></i> Base de données créée';
                    echo '</div>';
                    
                    // Créer la table users
                    $pdo->exec("
                        CREATE TABLE IF NOT EXISTS users (
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
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
                    ");
                    
                    echo '<div class="alert alert-success">';
                    echo '<i class="fas fa-check-circle"></i> Table users créée';
                    echo '</div>';
                    
                    // Vérifier si l'admin existe déjà
                    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'admin' OR email = 'admin@pharmacie.com'");
                    $stmt->execute();
                    
                    if (!$stmt->fetch()) {
                        // Hasher le mot de passe
                        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
                        
                        // Insérer l'admin
                        $stmt = $pdo->prepare("
                            INSERT INTO users (username, email, password, full_name, user_role) 
                            VALUES ('admin', 'admin@pharmacie.com', ?, 'Administrateur', 'admin')
                        ");
                        $stmt->execute([$hashed_password]);
                        
                        echo '<div class="alert alert-success">';
                        echo '<i class="fas fa-check-circle"></i> Compte administrateur créé';
                        echo '</div>';
                    }
                    
                    // Créer la table medicaments
                    $pdo->exec("
                        CREATE TABLE IF NOT EXISTS medicaments (
                            id INT PRIMARY KEY AUTO_INCREMENT,
                            name VARCHAR(200) NOT NULL,
                            description TEXT,
                            code VARCHAR(50) UNIQUE,
                            quantity INT DEFAULT 0,
                            min_quantity INT DEFAULT 10,
                            buy_price DECIMAL(10,2) NOT NULL,
                            sell_price DECIMAL(10,2) NOT NULL,
                            expiration_date DATE,
                            categorie VARCHAR(100),
                            fournisseur_id INT NULL,
                            supplier VARCHAR(200),
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                            INDEX idx_name (name),
                            INDEX idx_category (categorie)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
                    ");
                    
                    echo '<div class="alert alert-success">';
                    echo '<i class="fas fa-check-circle"></i> Table medicaments créée';
                    echo '</div>';
                    
                    // Créer la table fournisseurs
                    $pdo->exec("
                        CREATE TABLE IF NOT EXISTS fournisseurs (
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
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
                    ");
                    
                    // Créer la table clients
                    $pdo->exec("
                        CREATE TABLE IF NOT EXISTS clients (
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
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
                    ");
                    
                    // Créer la table ventes
                    $pdo->exec("
                        CREATE TABLE IF NOT EXISTS ventes (
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
                            FOREIGN KEY (utilisateur_id) REFERENCES users(id),
                            INDEX idx_date_vente (created_at)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
                    ");
                    
                    // Créer la table details_vente
                    $pdo->exec("
                        CREATE TABLE IF NOT EXISTS details_vente (
                            id INT PRIMARY KEY AUTO_INCREMENT,
                            vente_id INT NOT NULL,
                            medicament_id INT NOT NULL,
                            quantite INT NOT NULL,
                            prix_unitaire DECIMAL(10,2) NOT NULL,
                            total_ligne DECIMAL(10,2) NOT NULL,
                            FOREIGN KEY (vente_id) REFERENCES ventes(id) ON DELETE CASCADE,
                            FOREIGN KEY (medicament_id) REFERENCES medicaments(id),
                            INDEX idx_vente (vente_id)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
                    ");
                    
                    // Créer la table mouvements_stock
                    $pdo->exec("
                        CREATE TABLE IF NOT EXISTS mouvements_stock (
                            id INT PRIMARY KEY AUTO_INCREMENT,
                            medicament_id INT NOT NULL,
                            utilisateur_id INT NOT NULL,
                            type_mouvement ENUM('entree', 'sortie', 'ajustement', 'retour') NOT NULL,
                            quantite INT NOT NULL,
                            motif VARCHAR(255),
                            reference_document VARCHAR(100),
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            FOREIGN KEY (medicament_id) REFERENCES medicaments(id),
                            FOREIGN KEY (utilisateur_id) REFERENCES users(id),
                            INDEX idx_date_mouvement (created_at)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
                    ");
                    
                    echo '<div class="alert alert-success">';
                    echo '<i class="fas fa-check-circle"></i> Tables de gestion (ventes, clients, fournisseurs) créées';
                    echo '</div>';
                    
                    // Créer fichier de configuration
                    $config_content = "<?php\n// Configuration générée automatiquement\n";
                    $config_content .= "define('DB_HOST', '$host');\n";
                    $config_content .= "define('DB_USER', '$user');\n";
                    $config_content .= "define('DB_PASS', '$pass');\n";
                    $config_content .= "define('DB_NAME', '$dbname');\n";
                    $config_content .= "?>";
                    
                    if (file_put_contents('config.php', $config_content)) {
                        echo '<div class="alert alert-success">';
                        echo '<i class="fas fa-check-circle"></i> Fichier config.php créé';
                        echo '</div>';
                    }
                    
                    echo '<div class="step">';
                    echo '<h3><i class="fas fa-check-circle" style="color:#27ae60"></i> Installation terminée !</h3>';
                    echo '<p>Le système a été installé avec succès.</p>';
                    echo '<p><strong>Compte administrateur :</strong></p>';
                    echo '<ul style="margin-left: 20px; margin-top: 10px;">';
                    echo '<li><strong>Email/Username :</strong> admin ou admin@pharmacie.com</li>';
                    echo '<li><strong>Mot de passe :</strong> admin123</li>';
                    echo '</ul>';
                    echo '<div style="margin-top: 20px;">';
                    echo '<a href="login.php" class="btn btn-success">';
                    echo '<i class="fas fa-sign-in-alt"></i> Aller à la page de connexion';
                    echo '</a>';
                    echo '</div>';
                    echo '</div>';
                    
                } catch (PDOException $e) {
                    echo '<div class="alert alert-error">';
                    echo '<i class="fas fa-exclamation-circle"></i> Erreur : ' . $e->getMessage();
                    echo '</div>';
                    
                    echo '<div class="step">';
                    echo '<h3><i class="fas fa-exclamation-triangle"></i> Solution alternative</h3>';
                    echo '<p>Exécutez ce code SQL dans phpMyAdmin :</p>';
                    echo '<div class="code-block">';
                    echo htmlspecialchars("
CREATE DATABASE IF NOT EXISTS pharma_system;
USE pharma_system;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    user_role ENUM('admin', 'staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (username, email, password, full_name, user_role) 
VALUES (
    'admin',
    'admin@pharmacie.com',
    '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Administrateur',
    'admin'
);
                    ");
                    echo '</div>';
                    echo '</div>';
                }
            } else {
            ?>
            
            <div class="step">
                <h3><i class="fas fa-database"></i> Étape 1 : Configuration MySQL</h3>
                <p>Entrez les informations de connexion à votre serveur MySQL :</p>
                <form method="POST">
                    <div style="margin: 20px 0;">
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Hôte MySQL :</label>
                            <input type="text" name="host" value="localhost" required 
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Utilisateur :</label>
                            <input type="text" name="user" value="root" required 
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Mot de passe :</label>
                            <input type="password" name="pass" value="" 
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Nom de la base :</label>
                            <input type="text" name="dbname" value="pharma_system" required 
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-play"></i> Démarrer l'installation
                    </button>
                </form>
            </div>
            
            <div class="step">
                <h3><i class="fas fa-info-circle"></i> Informations importantes</h3>
                <p>Cette installation va :</p>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    <li>Créer la base de données</li>
                    <li>Créer les tables nécessaires</li>
                    <li>Créer un compte administrateur</li>
                    <li>Générer le fichier de configuration</li>
                </ul>
                <p style="margin-top: 15px; color: #e74c3c;">
                    <i class="fas fa-exclamation-triangle"></i>
                    Assurez-vous que MySQL est démarré avant de continuer.
                </p>
            </div>
            
            <?php } ?>
        </div>
        
        <div class="footer">
            <p>© 2024 PharmaGest - Système de gestion de pharmacie</p>
        </div>
    </div>
</body>
</html>