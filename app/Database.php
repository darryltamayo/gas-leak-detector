<?php

/**
 * Database Connection Class
 * 
 * Singleton pattern for database connection management
 */

class Database
{
    private static ?PDO $instance = null;
    
    /**
     * Get PDO instance
     * 
     * @return PDO
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                $config = require ROOT_PATH . '/config/database.php';
                
                $dsn = sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                    $config['host'],
                    $config['port'],
                    $config['database'],
                    $config['charset']
                );
                
                self::$instance = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    $config['options']
                );
                
                // Set MySQL timezone to match PHP timezone (Asia/Manila for Philippines)
                self::$instance->exec("SET time_zone = '+08:00'");
                
            } catch (PDOException $e) {
                error_log('Database Connection Error: ' . $e->getMessage());
                throw new Exception('Could not connect to database');
            }
        }
        
        return self::$instance;
    }
    
    /**
     * Prevent cloning
     */
    private function __clone() {}
    
    /**
     * Prevent unserialization
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}
