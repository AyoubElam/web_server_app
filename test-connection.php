<?php
// Start session
session_start();

// Include configuration
require_once 'config.php';
require_once 'utils.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get connection parameters
$host = $_POST['host'] ?? '';
$port = $_POST['port'] ?? '3306';
$dbname = $_POST['dbname'] ?? '';
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Validate required fields
if (empty($host) || empty($username)) {
    echo json_encode(['success' => false, 'message' => 'Host and username are required']);
    exit;
}

try {
    // Create DSN
    $dsn = "mysql:host=$host;port=$port";
    if (!empty($dbname)) {
        $dsn .= ";dbname=$dbname";
    }
    
    // Attempt connection
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5 // 5 second timeout
    ]);
    
    // Get server info
    $serverInfo = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
    
    // Log successful connection
    logMessage("Successful connection test from {$_SERVER['REMOTE_ADDR']} to $host:$port");
    
    // Return success response
    echo json_encode([
        'success' => true, 
        'message' => 'Connection successful',
        'server_info' => $serverInfo
    ]);
} catch (PDOException $e) {
    // Log failed connection attempt
    logMessage("Failed connection test from {$_SERVER['REMOTE_ADDR']} to $host:$port: {$e->getMessage()}", 'ERROR');
    
    // Return error response
    echo json_encode([
        'success' => false, 
        'message' => 'Connection failed: ' . $e->getMessage()
    ]);
}

