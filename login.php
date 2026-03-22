<?php
session_start();

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gestion_pharmacie');

// Connexion à la base de données
function getDB() {
    static $db = null;
    
    if ($db === null) {
        try {
            $db = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $e) {
            die("<div class='alert alert-error'>Erreur de connexion à la base de données: " . $e->getMessage() . "</div>");
        }
    }
    return $db;
}

$error = '';
$success = '';
$showRegisterForm = false;

// Traitement de l'inscription
if (isset($_POST['register'])) {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($full_name) || empty($email) || empty($username) || empty($password)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresse email invalide.";
    } elseif ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($password) < 6) {
        $error = "Le mot de passe doit contenir au moins 6 caractères.";
    } else {
        try {
            $db = getDB();
            
            // Vérifier si l'email existe déjà
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
            $stmt->execute([$email, $username]);
            
            if ($stmt->fetch()) {
                $error = "Cet email ou nom d'utilisateur est déjà utilisé.";
            } else {
                // Hasher le mot de passe
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insérer le nouvel utilisateur
                $stmt = $db->prepare("
                    INSERT INTO users (full_name, email, username, password, user_role) 
                    VALUES (?, ?, ?, ?, 'staff')
                ");
                
                $stmt->execute([$full_name, $email, $username, $hashed_password]);
                
                $success = "Compte créé avec succès ! Vous pouvez maintenant vous connecter.";
                $showRegisterForm = false;
            }
        } catch(PDOException $e) {
            $error = "Erreur lors de la création du compte: " . $e->getMessage();
        }
    }
    
    if ($error) {
        $showRegisterForm = true;
    }
}

// Traitement de la connexion
if (isset($_POST['login'])) {
    $email = trim($_POST['login_email'] ?? '');
    $password = $_POST['login_password'] ?? '';
    
    if (!empty($email) && !empty($password)) {
        try {
            $db = getDB();
            
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
            $stmt->execute([$email, $email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['user_role'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['username'] = $user['username'];
                
                // Mettre à jour la dernière connexion
                $updateStmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $updateStmt->execute([$user['id']]);
                
                header('Location: index.php');
                exit();
            } else {
                $error = "Email/Username ou mot de passe incorrect.";
            }
        } catch(PDOException $e) {
            $error = "Erreur de connexion: " . $e->getMessage();
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}

// Basculer entre login et register
if (isset($_GET['action']) && $_GET['action'] === 'register') {
    $showRegisterForm = true;
}

// Fonction de traduction simple (si i18n.php n'existe pas)
if (!function_exists('__')) {
    function __($key) {
        // Traductions de base
        $translations = [
            'login_title' => 'Connexion | PharmaGest',
            'pharma_gest' => 'PharmaGest',
            'login_subtitle' => 'Gestion de pharmacie intelligente',
            'username' => 'Email ou nom d\'utilisateur',
            'password' => 'Mot de passe',
            'login_btn' => 'Se connecter',
            'create_account' => 'Créer un compte',
            'dir' => 'ltr'
        ];
        return $translations[$key] ?? $key;
    }
}
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(__("login_title")); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ===== STYLES COMPLETS INTÉGRÉS ===== */
        :root {
            --primary: #4F46E5;
            --primary-dark: #4338CA;
            --primary-light: #EEF2FF;
            --success: #10B981;
            --warning: #F59E0B;
            --error: #EF4444;
            --gray-50: #F9FAFB;
            --gray-100: #F3F4F6;
            --gray-200: #E5E7EB;
            --gray-300: #D1D5DB;
            --gray-400: #9CA3AF;
            --gray-500: #6B7280;
            --gray-600: #4B5563;
            --gray-700: #374151;
            --gray-800: #1F2937;
            --gray-900: #111827;
--bg-body: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            --bg-card: rgba(255, 255, 255, 0.95);
            --text-primary: var(--gray-800);
            --text-secondary: var(--gray-600);
            --border-solid: var(--gray-200);
            --input-bg: white;
            --input-border: var(--gray-300);
            --shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        @media (prefers-color-scheme: dark) {
            :root {
--bg-body: linear-gradient(135deg, #1e3a8a 0%, #1e1b4b 100%);
                --bg-card: rgba(31, 41, 55, 0.95);
                --text-primary: var(--gray-100);
                --text-secondary: var(--gray-300);
                --border-solid: var(--gray-700);
                --input-bg: var(--gray-800);
                --input-border: var(--gray-600);
                --shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            }
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-body);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
            overflow-x: hidden;
        }
        .bg-shape {
            position: fixed;
            z-index: -1;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(60px);
            border-radius: 50%;
            pointer-events: none;
        }
        .bg-shape-1 {
            width: 300px;
            height: 300px;
            top: -100px;
            left: -100px;
background: radial-gradient(circle, rgba(59,130,246,0.3) 0%, rgba(59,130,246,0) 70%);
        }
        .bg-shape-2 {
            width: 400px;
            height: 400px;
            bottom: -150px;
            right: -150px;
background: radial-gradient(circle, rgba(30,58,138,0.3) 0%, rgba(30,58,138,0) 70%);
        }
        .login-container {
            max-width: 480px;
            width: 100%;
            background: var(--bg-card);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .login-header {
            text-align: center;
            padding: 2rem 2rem 1rem;
            background: linear-gradient(135deg, rgba(79,70,229,0.1) 0%, rgba(118,75,162,0.1) 100%);
        }
        .login-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        .login-header h1 i {
            margin-right: 10px;
        }
        .login-header p {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        .card-body {
            padding: 1.5rem 2rem;
        }
        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--border-solid);
        }
        .tab {
            flex: 1;
            text-align: center;
            padding: 0.75rem;
            font-weight: 600;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        .tab.active {
            color: var(--primary);
        }
        .tab.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--primary);
            border-radius: 2px;
        }
        .tab:hover:not(.active) {
            color: var(--primary);
            background: rgba(79,70,229,0.05);
        }
        .form-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }
        .form-content.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-primary);
            font-size: 0.9rem;
        }
        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .icon-left {
            position: absolute;
            left: 12px;
            color: var(--gray-400);
            font-size: 1rem;
            pointer-events: none;
        }
        .input-wrapper input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 1px solid var(--input-border);
            border-radius: 12px;
            background: var(--input-bg);
            color: var(--text-primary);
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        .input-wrapper input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79,70,229,0.1);
        }
        .toggle-password {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--gray-400);
            font-size: 1rem;
            padding: 0;
            display: flex;
            align-items: center;
        }
        .toggle-password:hover {
            color: var(--primary);
        }
        .password-strength {
            margin-top: 0.5rem;
            height: 4px;
            background: var(--gray-200);
            border-radius: 4px;
            overflow: hidden;
        }
        .strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s ease, background 0.3s ease;
            border-radius: 4px;
        }
        .strength-weak { background: var(--error); }
        .strength-medium { background: var(--warning); }
        .strength-strong { background: var(--success); }
        .password-requirements {
            margin-top: 0.5rem;
        }
        .requirement {
            font-size: 0.75rem;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .requirement i {
            font-size: 0.5rem;
            transition: color 0.2s;
        }
        .requirement.met {
            color: var(--success);
        }
        .btn {
            width: 100%;
            padding: 0.85rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79,70,229,0.3);
        }
        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
            animation: slideDown 0.3s ease;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .alert-error {
            background: rgba(239,68,68,0.1);
            border-left: 4px solid var(--error);
            color: var(--error);
        }
        .alert-success {
            background: rgba(16,185,129,0.1);
            border-left: 4px solid var(--success);
            color: var(--success);
        }
        .demo-info {
            margin-top: 2rem;
            padding: 1rem;
            background: rgba(79,70,229,0.05);
            border-radius: 12px;
            text-align: center;
        }
        .demo-info h4 {
            color: var(--primary);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .demo-info p {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }
        .demo-info code {
            background: var(--gray-200);
            padding: 0.2rem 0.4rem;
            border-radius: 6px;
            font-family: monospace;
            font-size: 0.8rem;
            color: var(--gray-800);
        }
        .card-footer {
            text-align: center;
            padding: 1rem;
            font-size: 0.75rem;
            color: var(--text-secondary);
            border-top: 1px solid var(--border-solid);
        }
        .card-footer a {
            color: var(--primary);
            text-decoration: none;
            transition: color 0.2s;
        }
        .card-footer a:hover {
            text-decoration: underline;
        }
        @media (max-width: 640px) {
            .login-container { max-width: 100%; margin: 1rem; }
            .card-body { padding: 1.5rem; }
            .login-header { padding: 1.5rem 1.5rem 1rem; }
            .login-header h1 { font-size: 1.5rem; }
        }
        @media (prefers-reduced-motion: reduce) {
            * { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
        }
    </style>
</head>
<body>
    <!-- Background Shapes -->
    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>

    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-clinic-medical"></i> PharmaGest</h1>
            <p>Gestion de pharmacie intelligente</p>
        </div>
        
        <div class="card-body">
            <!-- Tabs -->
            <div class="tabs">
                <div class="tab <?php echo !$showRegisterForm ? 'active' : ''; ?>" onclick="showLogin()">
                    Connexion
                </div>
                <div class="tab <?php echo $showRegisterForm ? 'active' : ''; ?>" onclick="showRegister()">
                    Création de compte
                </div>
            </div>
            
            <!-- Messages d'alerte -->
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <!-- Formulaire de connexion -->
            <div id="loginForm" class="form-content <?php echo !$showRegisterForm ? 'active' : ''; ?>">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="login_email">Email ou nom d'utilisateur</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user icon-left"></i>
                            <input type="text" id="login_email" name="login_email" 
                                   placeholder="Email ou Nom d'utilisateur" required
                                   value="<?php echo isset($_POST['login_email']) ? htmlspecialchars($_POST['login_email']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="login_password">Mot de passe</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock icon-left"></i>
                            <input type="password" id="login_password" name="login_password" 
                                   placeholder="Votre mot de passe" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('login_password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <button type="submit" name="login" class="btn btn-primary">
                        Se connecter <i class="fas fa-arrow-right" style="margin-left: 5px;"></i>
                    </button>
                </form>
            </div>
            
            <!-- Formulaire d'inscription -->
            <div id="registerForm" class="form-content <?php echo $showRegisterForm ? 'active' : ''; ?>">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="full_name">Nom complet</label>
                        <div class="input-wrapper">
                            <i class="fas fa-id-card icon-left"></i>
                            <input type="text" id="full_name" name="full_name" 
                                   placeholder="Dr. Jean Dupont" required
                                   value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Adresse email</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope icon-left"></i>
                            <input type="email" id="email" name="email" 
                                   placeholder="contact@pharmacie.com" required
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Nom d'utilisateur</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user-tag icon-left"></i>
                            <input type="text" id="username" name="username" 
                                   placeholder="jeandupont" required
                                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock icon-left"></i>
                            <input type="password" id="password" name="password" 
                                   placeholder="Au moins 6 caractères" required
                                   oninput="checkPasswordStrength()">
                            <button type="button" class="toggle-password" onclick="togglePassword('password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength">
                            <div id="strengthBar" class="strength-bar"></div>
                        </div>
                        <div class="password-requirements">
                            <div id="lengthReq" class="requirement">
                                <i class="fas fa-circle"></i>
                                <span>Au moins 6 caractères</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirmer le mot de passe</label>
                        <div class="input-wrapper">
                            <i class="fas fa-check-circle icon-left"></i>
                            <input type="password" id="confirm_password" name="confirm_password" 
                                   placeholder="Retapez votre mot de passe" required
                                   oninput="checkPasswordMatch()">
                            <button type="button" class="toggle-password" onclick="togglePassword('confirm_password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div id="passwordMatch" style="font-size: 13px; margin-top: 8px; font-weight: 500;"></div>
                    </div>
                    
                    <button type="submit" name="register" class="btn btn-primary">
                        Créer un compte <i class="fas fa-user-plus" style="margin-left: 5px;"></i>
                    </button>
                </form>
            </div>
            
            <!-- Informations de démo -->
            
        </div>
        
        <div class="card-footer text-center">
            <p>© 2024 PharmaGest.</p>
            <p style="margin-top: 8px;">
                <a href="install.php"><i class="fas fa-cog"></i> Page d'installation</a>
            </p>
        </div>
    </div>

    <script>
        // Basculer entre login et register
        function showLogin() {
            document.getElementById('loginForm').classList.add('active');
            document.getElementById('registerForm').classList.remove('active');
            document.querySelectorAll('.tab')[0].classList.add('active');
            document.querySelectorAll('.tab')[1].classList.remove('active');
            window.history.pushState({}, '', '?action=login');
            
            const demoInfo = document.getElementById('demoInfo');
            if (demoInfo) demoInfo.style.display = 'block';
        }
        
        function showRegister() {
            document.getElementById('registerForm').classList.add('active');
            document.getElementById('loginForm').classList.remove('active');
            document.querySelectorAll('.tab')[1].classList.add('active');
            document.querySelectorAll('.tab')[0].classList.remove('active');
            window.history.pushState({}, '', '?action=register');
            
            const demoInfo = document.getElementById('demoInfo');
            if (demoInfo) demoInfo.style.display = 'none';
        }
        
        // Afficher/masquer le mot de passe
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Vérifier la force du mot de passe
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthBar = document.getElementById('strengthBar');
            const lengthReq = document.getElementById('lengthReq');
            
            strengthBar.className = 'strength-bar';
            strengthBar.style.width = '0%';
            
            if (password.length === 0) {
                lengthReq.querySelector('i').style.color = '#CBD5E1';
                return;
            }
            
            if (password.length >= 6) {
                lengthReq.classList.add('met');
                lengthReq.querySelector('i').style.color = 'var(--success)';
                
                let strength = 0;
                if (password.length >= 8) strength++;
                if (/[A-Z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[^A-Za-z0-9]/.test(password)) strength++;
                
                const width = (strength + 1) * 25;
                strengthBar.style.width = Math.min(width, 100) + '%';
                
                if (strength <= 1) {
                    strengthBar.className = 'strength-bar strength-weak';
                } else if (strength === 2) {
                    strengthBar.className = 'strength-bar strength-medium';
                } else {
                    strengthBar.className = 'strength-bar strength-strong';
                }
            } else {
                lengthReq.classList.remove('met');
                lengthReq.querySelector('i').style.color = 'var(--error)';
            }
        }
        
        // Vérifier la correspondance des mots de passe
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;
            const matchDiv = document.getElementById('passwordMatch');
            
            if (confirm.length === 0) {
                matchDiv.innerHTML = '';
                return;
            }
            
            if (password === confirm) {
                matchDiv.innerHTML = '<i class="fas fa-check-circle" style="margin-right: 5px;"></i> Les mots de passe correspondent';
                matchDiv.style.color = 'var(--success)';
            } else {
                matchDiv.innerHTML = '<i class="fas fa-times-circle" style="margin-right: 5px;"></i> Les mots de passe ne correspondent pas';
                matchDiv.style.color = 'var(--error)';
            }
        }
        
        // Auto-focus
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($showRegisterForm): ?>
                const fn = document.getElementById('full_name');
                if (fn) fn.focus();
            <?php else: ?>
                const le = document.getElementById('login_email');
                if (le) le.focus();
            <?php endif; ?>
        });
    </script>
</body>
</html>