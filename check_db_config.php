<?php
// Simple database configuration checker

echo "=== Current .env Settings ===\n\n";

$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile);
    $dbVars = ['DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'];
    
    foreach ($lines as $line) {
        $line = trim($line);
        foreach ($dbVars as $var) {
            if (strpos($line, $var . '=') === 0) {
                echo $line . "\n";
            }
        }
    }
} else {
    echo "ERROR: .env file not found!\n";
}

echo "\n=== Testing MySQL Connection ===\n\n";

try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;port=3306;dbname=ukoo',
        'root',
        ''
    );
    echo "✓ MySQL connection successful!\n";
    echo "✓ Database 'ukoo' exists and is accessible!\n";
    
    // Show databases
    $stmt = $pdo->query("SHOW DATABASES LIKE 'ukoo'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Database 'ukoo' confirmed!\n";
    }
} catch (PDOException $e) {
    echo "✗ MySQL connection failed: " . $e->getMessage() . "\n\n";
    echo "Please ensure:\n";
    echo "1. XAMPP MySQL is running\n";
    echo "2. Database 'ukoo' exists (create it in phpMyAdmin)\n";
    echo "3. MySQL is accessible on port 3306\n";
}
