<?php
// fix_db_full.php - Complete DB recreation with correct schema
echo '<h2>🛠️ Pharmacy DB Full Fix</h2>';
echo '<pre>';

// Check if schema.sql exists
if (!file_exists('full_schema.sql')) {
    die('❌ full_schema.sql not found!');
}

try {
    // Connect to MySQL (no DB yet for drop/create)
    $pdo = new PDO('mysql:host=localhost;charset=utf8', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo '✅ MySQL connected<br>';

    // Read and execute schema
    $schema = file_get_contents('full_schema.sql');
    if ($schema === false) {
        die('❌ Cannot read full_schema.sql');
    }

    // Execute schema SQL
    $pdo->exec($schema);
    echo '✅ Full schema executed successfully!<br>';

    // Verify key tables
    $pdo->exec('USE gestion_pharmacie');
    $tables = ['medicaments', 'users', 'clients', 'ventes'];
    foreach ($tables as $table) {
        $result = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($result->fetch()) {
            $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
            echo "✅ $table: {$count} rows<br>";
        } else {
            echo "❌ $table missing<br>";
        }
    }

    echo '</pre>';
    echo '<div style="background: #d4edda; padding: 20px; border-radius: 8px; border: 1px solid #c3e6cb;">';
    echo '<h3>🎉 SUCCESS! DB fixed:</h3>';
    echo '<ul>';
    echo '<li><strong>Admin login:</strong> admin / admin123</li>';
    echo '<li>Sample data added (3 medicaments, 1 client)</li>';
    echo '<li><a href="login.php">→ Go to Login</a> | <a href="index.php">→ Dashboard</a></li>';
    echo '</ul>';
    echo '</div>';

} catch (Exception $e) {
    echo '<div style="background: #f8d7da; padding: 20px; border-radius: 8px; border: 1px solid #f5c6cb; color: #721c24;">';
    echo '❌ Error: ' . $e->getMessage();
    echo '</div>';
}
?>

