<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation Test - Cinema Ticketing System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #667eea;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .test-item {
            margin: 15px 0;
            padding: 10px;
            border-left: 4px solid #ddd;
            background: #f9f9f9;
        }
        .success {
            border-left-color: #28a745;
            background: #d4edda;
        }
        .error {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        .info {
            padding: 15px;
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 4px;
            margin: 20px 0;
        }
        code {
            background: #333;
            color: #fff;
            padding: 2px 6px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>🔧 Installation Test</h1>
        
        <p>This page checks if your Cinema Ticketing System is properly configured.</p>
        
        <?php
        $allGood = true;
        
        // Test 1: PHP Version
        echo '<div class="test-item ' . (version_compare(PHP_VERSION, '7.4.0', '>=') ? 'success' : 'error') . '">';
        echo '<strong>PHP Version:</strong> ' . PHP_VERSION;
        if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
            echo ' ✓ OK';
        } else {
            echo ' ✗ PHP 7.4 or higher required';
            $allGood = false;
        }
        echo '</div>';
        
        // Test 2: Required Extensions
        $extensions = ['mysqli', 'session'];
        foreach ($extensions as $ext) {
            echo '<div class="test-item ' . (extension_loaded($ext) ? 'success' : 'error') . '">';
            echo '<strong>Extension ' . $ext . ':</strong> ';
            if (extension_loaded($ext)) {
                echo 'Loaded ✓';
            } else {
                echo 'Not loaded ✗';
                $allGood = false;
            }
            echo '</div>';
        }
        
        // Test 3: Config file exists
        echo '<div class="test-item ' . (file_exists('config/db_config.php') ? 'success' : 'error') . '">';
        echo '<strong>Configuration File:</strong> ';
        if (file_exists('config/db_config.php')) {
            echo 'Found ✓';
        } else {
            echo 'config/db_config.php not found ✗';
            $allGood = false;
        }
        echo '</div>';
        
        // Test 4: Database Connection
        if (file_exists('config/db_config.php')) {
            require_once 'config/db_config.php';
            try {
                $conn = @getDBConnection();
                if ($conn && !$conn->connect_error) {
                    echo '<div class="test-item success">';
                    echo '<strong>Database Connection:</strong> Connected ✓';
                    echo '</div>';
                    
                    // Test 5: Check tables
                    $tables = ['memberProfile', 'memberCashCard', 'cinema', 'theater', 'movie', 'showing', 'bookingRecord', 'seatCondition'];
                    $missingTables = [];
                    
                    foreach ($tables as $table) {
                        $result = $conn->query("SHOW TABLES LIKE '$table'");
                        if ($result->num_rows == 0) {
                            $missingTables[] = $table;
                        }
                    }
                    
                    echo '<div class="test-item ' . (empty($missingTables) ? 'success' : 'error') . '">';
                    echo '<strong>Database Tables:</strong> ';
                    if (empty($missingTables)) {
                        echo 'All tables exist ✓';
                    } else {
                        echo 'Missing tables: ' . implode(', ', $missingTables) . ' ✗';
                        $allGood = false;
                    }
                    echo '</div>';
                    
                    closeDBConnection($conn);
                } else {
                    echo '<div class="test-item error">';
                    echo '<strong>Database Connection:</strong> Failed ✗<br>';
                    echo 'Error: ' . ($conn->connect_error ?? 'Unknown error');
                    echo '</div>';
                    $allGood = false;
                }
            } catch (Exception $e) {
                echo '<div class="test-item error">';
                echo '<strong>Database Connection:</strong> Failed ✗<br>';
                echo 'Error: ' . $e->getMessage();
                echo '</div>';
                $allGood = false;
            }
        }
        
        // Summary
        echo '<hr style="margin: 30px 0; border: 0; border-top: 1px solid #ddd;">';
        if ($allGood) {
            echo '<div class="info" style="background: #d4edda; border-color: #c3e6cb; color: #155724;">';
            echo '<h3 style="margin-top: 0;">✅ Installation Complete!</h3>';
            echo '<p>Your Cinema Ticketing System is properly configured and ready to use.</p>';
            echo '<p><a href="index.php" style="color: #667eea; text-decoration: none; font-weight: bold;">→ Go to Home Page</a></p>';
            echo '</div>';
        } else {
            echo '<div class="info" style="background: #f8d7da; border-color: #f5c6cb; color: #721c24;">';
            echo '<h3 style="margin-top: 0;">⚠️ Installation Issues Detected</h3>';
            echo '<p>Please fix the errors above before using the system.</p>';
            echo '<p><strong>Common solutions:</strong></p>';
            echo '<ul>';
            echo '<li>Update <code>config/db_config.php</code> with your database credentials</li>';
            echo '<li>Import <code>config/schema.sql</code> into your MySQL database</li>';
            echo '<li>Ensure PHP extensions are enabled in your php.ini</li>';
            echo '</ul>';
            echo '</div>';
        }
        ?>
        
        <div class="info">
            <h3 style="margin-top: 0;">📚 Next Steps</h3>
            <ol>
                <li>If not done already, import the database schema: <code>mysql -u username -p &lt; config/schema.sql</code></li>
                <li>Visit <a href="index.php">the home page</a></li>
                <li>Login with demo account: username <code>demouser</code>, password <code>password123</code></li>
                <li>Explore the features and start booking!</li>
            </ol>
        </div>
    </div>
</body>
</html>
