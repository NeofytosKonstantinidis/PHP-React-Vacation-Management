<?php
class Database {
    private static $instance = null;
    private $connection;
    private $config;
    
    private function __construct() {
        // Load configuration
        $this->config = require __DIR__ . '/config.php';
        $dbConfig = $this->config['database'];
        
        try {
            $this->connection = new PDO(
                "mysql:host={$dbConfig['host']};dbname={$dbConfig['db_name']}",
                $dbConfig['username'],
                $dbConfig['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            // Set charset after connection
            $this->connection->exec("SET NAMES '{$dbConfig['charset']}'");
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Prevent cloning
    private function __clone() {}
    
    // Prevent unserialization
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

// Helper function for backward compatibility
function getPDOConnection() {
    return Database::getInstance()->getConnection();
}