<?php
// tests/bootstrap.php
// Bootstrap file for PHPUnit tests

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Define test mode
define('TEST_MODE', true);

// Load composer autoloader if exists
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Load required files
require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/utils/Response.php';
require_once __DIR__ . '/../src/utils/Auth.php';
require_once __DIR__ . '/../src/utils/Validator.php';

// Helper function for tests
function getTestPDO() {
    $config = require __DIR__ . '/../src/config/config.php';
    $dbConfig = $config['database'];
    
    try {
        // Use test database instead of production database
        $pdo = new PDO(
            "mysql:host={$dbConfig['host']};dbname=leaves_management_test;charset={$dbConfig['charset']}",
            $dbConfig['username'],
            $dbConfig['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        throw new Exception("Test database connection failed: " . $e->getMessage());
    }
}

// Clean test database function
function cleanTestDatabase() {
    $pdo = getTestPDO();
    
    $tables = [
        'audit_log',
        'vacation_requests',
        'work_schedule',
        'users',
        'vacation_status_types',
        'schedule_types',
        'role_types'
    ];
    
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    foreach ($tables as $table) {
        $pdo->exec("TRUNCATE TABLE {$table}");
    }
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
}

// Removed echo to prevent "headers already sent" errors in Response tests
// echo "PHPUnit Bootstrap loaded successfully.\n";