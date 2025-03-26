<?php
/**
 * Database Configuration
 * 
 * This file loads database configuration from environment variables
 * or falls back to default values if not set.
 */

// Function to get environment variable with fallback
function getEnv($key, $default = null) {
    $value = getenv($key);
    if ($value !== false) {
        return $value;
    }
    
    // Check for Apache SetEnv variables
    if (isset($_SERVER[$key])) {
        return $_SERVER[$key];
    }
    
    return $default;
}

// Database configuration
$dbConfig = [
    'host'     => 'localhost',
    'port'     => '3306',
    'dbname'   => 'db_connect_app',
    'username' => 'root',   
    'password' => ''  // XAMPP MySQL has no password by default
];

// Application configuration
$appConfig = [
    'debug'    => getEnv('APP_DEBUG', 'false') === 'true',
    'timezone' => getEnv('APP_TIMEZONE', 'UTC'),
    'charset'  => getEnv('APP_CHARSET', 'UTF-8')
];

// Set timezone
date_default_timezone_set($appConfig['timezone']);

// Error reporting based on debug mode
if ($appConfig['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

