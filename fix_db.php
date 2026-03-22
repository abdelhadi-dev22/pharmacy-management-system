<?php
// fix_db.php - Script to create the missing users table
require_once 'includes/db.php';

try {
    $db = getDB();

    // Check if users table exists
    $result = $db->query("SHOW TABLES LIKE 'users'");
    $tableExists = $result->fetch();

    if (!$tableExists) {
        echo "Creating users table...<br>";

        // Create users table
        $db->exec("
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        echo "Users table created successfully.<br>";

        // Check if admin user exists
        $stmt = $db->prepare("SELECT id FROM users WHERE username = 'admin'");
        $stmt->execute();
        $adminExists = $stmt->fetch();

        if (!$adminExists) {
            // Insert admin user
            $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $db->prepare("
                INSERT INTO users (username, email, password, full_name, user_role)
                VALUES ('admin', 'admin@pharmacie.com', ?, 'Administrateur', 'admin')
            ");
            $stmt->execute([$hashed_password]);

            echo "Admin user created successfully.<br>";
            echo "Username: admin<br>";
            echo "Email: admin@pharmacie.com<br>";
            echo "Password: admin123<br>";
        } else {
            echo "Admin user already exists.<br>";
        }
    } else {
        echo "Users table already exists.<br>";
    }

    echo "<br><strong>Database fix completed successfully!</strong><br>";
    echo "<a href='login.php'>Go to Login Page</a>";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
