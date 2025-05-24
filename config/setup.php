<?php
require_once 'database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Read and execute the schema
    $sql = file_get_contents(__DIR__ . '/schema.sql');
    $db->exec($sql);
    
    echo "Database tables created successfully!\n";
} catch(PDOException $e) {
    echo "Error creating tables: " . $e->getMessage() . "\n";
}
?> 