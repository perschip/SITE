<?php
// Include database connection
require_once 'includes/db.php';

// Output all tables
echo "<h2>All Tables</h2>";
$tables_query = "SHOW TABLES";
$tables_result = $pdo->query($tables_query);
echo "<ul>";
while ($table = $tables_result->fetch(PDO::FETCH_NUM)) {
    echo "<li>" . $table[0] . "</li>";
}
echo "</ul>";

// Try to find pages table
echo "<h2>Looking for Pages Table</h2>";
$potential_tables = ['pages', 'page', 'custom_pages', 'custom_page', 'site_pages'];
foreach ($potential_tables as $table_name) {
    $check_query = "SHOW TABLES LIKE '$table_name'";
    $result = $pdo->query($check_query);
    if ($result->rowCount() > 0) {
        echo "<p>Found table: $table_name</p>";
        
        // Show table structure
        echo "<h3>Table Structure</h3>";
        $cols_query = "DESCRIBE $table_name";
        $cols_result = $pdo->query($cols_query);
        echo "<ul>";
        while ($col = $cols_result->fetch(PDO::FETCH_ASSOC)) {
            echo "<li>" . $col['Field'] . " - " . $col['Type'] . "</li>";
        }
        echo "</ul>";
        
        // Show sample data
        echo "<h3>Sample Data</h3>";
        $data_query = "SELECT * FROM $table_name LIMIT 5";
        $data_result = $pdo->query($data_query);
        echo "<table border='1'>";
        
        // Headers
        $first = true;
        while ($row = $data_result->fetch(PDO::FETCH_ASSOC)) {
            if ($first) {
                echo "<tr>";
                foreach (array_keys($row) as $key) {
                    echo "<th>" . $key . "</th>";
                }
                echo "</tr>";
                $first = false;
            }
            
            // Data
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
}
?>