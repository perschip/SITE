<?php
// fix-stream-charset.php - Fix character set for stream_schedule table

require_once 'config.php';

try {
    echo "Updating database character set for emoji support...\n\n";
    
    // Update the stream_schedule table to use utf8mb4
    $sql = "ALTER TABLE stream_schedule 
            MODIFY title VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            MODIFY description TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "✓ Updated stream_schedule table character set\n";
    
    // Update the database connection to use utf8mb4
    $sql = "SET NAMES utf8mb4";
    $pdo->exec($sql);
    echo "✓ Set database connection character set\n";
    
    // Also update other tables that might have text fields
    $tables = [
        ['blog_posts', ['title', 'content', 'slug']],
        ['pages', ['title', 'content', 'slug']],
        ['ebay_listings', ['title', 'description']],
        ['admin_users', ['username', 'email']],
        ['site_settings', ['setting_key', 'setting_value']]
    ];
    
    foreach ($tables as $table) {
        $tableName = $table[0];
        $columns = $table[1];
        
        foreach ($columns as $column) {
            try {
                // Get column definition
                $stmt = $pdo->query("DESCRIBE {$tableName} {$column}");
                $columnInfo = $stmt->fetch();
                
                if ($columnInfo) {
                    $columnType = $columnInfo['Type'];
                    $sql = "ALTER TABLE {$tableName} MODIFY {$column} {$columnType} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
                    $pdo->exec($sql);
                    echo "✓ Updated {$tableName}.{$column}\n";
                }
            } catch (Exception $e) {
                echo "⚠ Could not update {$tableName}.{$column}: " . $e->getMessage() . "\n";
            }
        }
    }
    
    // Update the database itself
    $sql = "ALTER DATABASE `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $pdo->exec($sql);
    echo "\n✓ Updated database character set\n";
    
    echo "\nAll done! Your database now supports emojis and all UTF-8 characters.\n";
    echo "You can now use emojis in stream titles and descriptions!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
?>