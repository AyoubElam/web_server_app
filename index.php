<?php
// Include configuration and utilities
require_once 'config.php';
require_once 'database.php';
require_once 'utils.php';

// Initialize the database connection
$db = new Database();
$connectionStatus = $db->isConnected();
$errorMessage = $db->getErrorMessage();
$serverInfo = $connectionStatus ? $db->getServerInfo() : null;
$databaseList = $connectionStatus ? $db->getDatabases() : [];

// Get server status
$serverStatus = [
    'php_version' => phpversion(),
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
    'server_time' => date('Y-m-d H:i:s'),
    'memory_usage' => formatBytes(memory_get_usage(true))
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Dashboard</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <i class="fas fa-database"></i>
                <h1>DB Connect</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="#" class="active">Dashboard</a></li>
                    <li><a href="#server-info">Server Info</a></li>
                    <li><a href="#databases">Databases</a></li>
                    <li><a href="#settings">Settings</a></li>
                </ul>
            </nav>
            <div class="theme-toggle">
                <i class="fas fa-moon"></i>
            </div>
        </header>

        <main>
            <section class="status-card <?php echo $connectionStatus ? 'success' : 'error'; ?>">
                <div class="status-icon">
                    <i class="fas <?php echo $connectionStatus ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                </div>
                <div class="status-content">
                    <h2><?php echo $connectionStatus ? 'Connection Successful' : 'Connection Failed'; ?></h2>
                    <p><?php echo $connectionStatus ? 'Successfully connected to the MySQL database.' : 'Failed to connect to the MySQL database.'; ?></p>
                    <?php if (!$connectionStatus): ?>
                        <div class="error-details">
                            <p><?php echo htmlspecialchars($errorMessage); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <div class="dashboard-grid">
                <section id="server-info" class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-server"></i> Server Information</h2>
                    </div>
                    <div class="card-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Host</span>
                                <span class="info-value"><?php echo htmlspecialchars($dbConfig['host']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Database</span>
                                <span class="info-value"><?php echo htmlspecialchars($dbConfig['dbname']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Port</span>
                                <span class="info-value"><?php echo htmlspecialchars($dbConfig['port']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Username</span>
                                <span class="info-value"><?php echo htmlspecialchars($dbConfig['username']); ?></span>
                            </div>
                            <?php if ($connectionStatus && $serverInfo): ?>
                                <div class="info-item">
                                    <span class="info-label">MySQL Version</span>
                                    <span class="info-value"><?php echo htmlspecialchars($serverInfo); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>

                <section class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-cogs"></i> System Status</h2>
                    </div>
                    <div class="card-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">PHP Version</span>
                                <span class="info-value"><?php echo htmlspecialchars($serverStatus['php_version']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Web Server</span>
                                <span class="info-value"><?php echo htmlspecialchars($serverStatus['server_software']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Server Time</span>
                                <span class="info-value"><?php echo htmlspecialchars($serverStatus['server_time']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Memory Usage</span>
                                <span class="info-value"><?php echo htmlspecialchars($serverStatus['memory_usage']); ?></span>
                            </div>
                        </div>
                    </div>
                </section>

                <?php if ($connectionStatus): ?>
                <section id="databases" class="card full-width">
                    <div class="card-header">
                        <h2><i class="fas fa-database"></i> Available Databases</h2>
                    </div>
                    <div class="card-body">
                        <?php if (count($databaseList) > 0): ?>
                            <div class="database-list">
                                <?php foreach ($databaseList as $database): ?>
                                    <div class="database-item">
                                        <i class="fas fa-database"></i>
                                        <span><?php echo htmlspecialchars($database); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p>No databases available or insufficient privileges to list them.</p>
                        <?php endif; ?>
                    </div>
                </section>
                <?php endif; ?>

                <section id="settings" class="card full-width">
                    <div class="card-header">
                        <h2><i class="fas fa-tools"></i> Connection Test</h2>
                    </div>
                    <div class="card-body">
                        <form id="connection-form" action="test-connection.php" method="post">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="host">Host</label>
                                    <input type="text" id="host" name="host" value="<?php echo htmlspecialchars($dbConfig['host']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="port">Port</label>
                                    <input type="text" id="port" name="port" value="<?php echo htmlspecialchars($dbConfig['port']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="dbname">Database Name</label>
                                    <input type="text" id="dbname" name="dbname" value="<?php echo htmlspecialchars($dbConfig['dbname']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($dbConfig['username']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" id="password" name="password" placeholder="Enter password">
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn primary">Test Connection</button>
                                <button type="reset" class="btn secondary">Reset</button>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> DB Connect Dashboard. All rights reserved.</p>
            <p>LAMP Stack Application</p>
        </footer>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>

