<?php
/**
 * Database Auto-Installer Script
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');

$message = '';
$error = '';

try {
    // 1. Connect to MySQL without database name
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS secure_login_db");
    
    // 3. Connect to the created database
    $pdo->exec("USE secure_login_db");

    // 4. Create users table
    $usersTable = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        is_verified TINYINT(1) DEFAULT 0,
        verification_token VARCHAR(255) DEFAULT NULL,
        reset_token VARCHAR(255) DEFAULT NULL,
        remember_token VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($usersTable);

    // 5. Create login_logs table
    $logsTable = "CREATE TABLE IF NOT EXISTS login_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        ip_address VARCHAR(45),
        login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('success', 'failed') DEFAULT 'success',
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($logsTable);

    // 6. Seed default user (Rehan Naveed)
    $check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check->execute(['Rehan Naveed', 'toku8ball@gmail.com']);
    if ($check->rowCount() === 0) {
        $hashedPass = password_hash('12345678', PASSWORD_BCRYPT);
        $seed = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $seed->execute(['Rehan Naveed', 'toku8ball@gmail.com', $hashedPass]);
    }

    $message = "Database created & default user seeded successfully!";
} catch (PDOException $e) {
    $error = "Installation failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup | Secure Login System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Database Installer</h1>
                <p>Initializing SecureAuth Database</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <strong>Error:</strong> <?php echo $error; ?>
                </div>
                <p style="text-align: center; color: var(--text-muted);">Make sure MySQL is running in your XAMPP panel.</p>
                <button onclick="window.location.reload();" class="btn-primary">Try Again</button>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="alert alert-success">
                    <strong>Success!</strong> <?php echo $message; ?>
                </div>
                <p style="text-align: center; color: var(--text-muted); margin-bottom: 25px;">You can now proceed to the application.</p>
                <a href="index.php" class="btn-primary" style="display: block; text-align: center; text-decoration: none; box-sizing: border-box;">Go to Login Page</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
