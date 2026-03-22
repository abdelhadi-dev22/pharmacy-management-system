<?php
require_once 'includes/db.php';
$db = getDB();

$output = "";
function dumpTable($db, $table, &$output) {
    $output .= "--- TABLE: $table ---\n";
    try {
        $stmt = $db->query("DESCRIBE `$table` ");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $col) {
            $output .= "  {$col['Field']} - {$col['Type']}\n";
        }
    } catch (Exception $e) {
        $output .= "  ERROR: " . $e->getMessage() . "\n";
    }
    $output .= "\n";
}

$stmt = $db->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $table) {
    dumpTable($db, $table, $output);
}

file_put_contents('schema_dump.txt', $output);
echo "Dump complete\n";
?>
