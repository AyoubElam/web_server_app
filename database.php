<?php
/**
 * Database Class
 * 
 * Handles database connections and operations
 */
class Database {
    private $pdo;
    private $connected = false;
    private $errorMessage = '';
    private $config;

    public function __construct() {
        global $dbConfig;
        $this->config = $dbConfig;
        $this->connect();
    }

    /**
     * Establish database connection
     */
    private function connect() {
        try {
            $dsn = "mysql:host={$this->config['host']};port={$this->config['port']};";
            
            // Add database name to DSN if provided
            if (!empty($this->config['dbname'])) {
                $dsn .= "dbname={$this->config['dbname']};";
            }
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            $this->pdo = new PDO($dsn, $this->config['username'], $this->config['password'], $options);
            $this->connected = true;
        } catch (PDOException $e) {
            $this->errorMessage = $e->getMessage();
            $this->connected = false;
        }
    }

    /**
     * Check if connection is established
     */
    public function isConnected() {
        return $this->connected;
    }

    /**
     * Get error message if connection failed
     */
    public function getErrorMessage() {
        return $this->errorMessage;
    }

    /**
     * Get server information
     */
    public function getServerInfo() {
        if (!$this->connected) {
            return null;
        }
        return $this->pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
    }

    /**
     * Get list of databases
     */
    public function getDatabases() {
        if (!$this->connected) {
            return [];
        }
        
        try {
            $stmt = $this->pdo->query("SHOW DATABASES");
            $databases = [];
            
            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                $databases[] = $row[0];
            }
            
            return $databases;
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Execute a query
     */
    public function query($sql, $params = []) {
        if (!$this->connected) {
            return false;
        }
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            $this->errorMessage = $e->getMessage();
            return false;
        }
    }

    /**
     * Get a single row
     */
    public function getRow($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetch() : false;
    }

    /**
     * Get multiple rows
     */
    public function getRows($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetchAll() : [];
    }
}

