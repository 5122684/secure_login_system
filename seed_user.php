<?php
/**
 * One-time script to insert a default user into the database.
 * DELETE this file after running it!
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'secure_login_db');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $username  = 'Rehan Naveed';
    $email     = 'toku8ball@gmail.com';
    $password  = password_hash('12345678', PASSWORD_BCRYPT);

    // Delete existing user with same username or email first (to avoid duplicates)
    $del = $pdo->prepare("DELETE FROM users WHERE username = ? OR email = ?");
    $del->execute([$username, $email]);

    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$username, $email, $password]);

    echo "<div style='font-family:sans-serif;max-width:500px;margin:50px auto;padding:30px;background:#1e293b;color:#f8fafc;border-radius:16px;'>";
    echo "<h2 style='color:#4ade80;'>✅ User Created Successfully!</h2>";
    echo "<p><strong>Username:</strong> Rehan Naveed</p>";
    echo "<p><strong>Email:</strong> toku8ball@gmail.com</p>";
    echo "<p><strong>Password:</strong> 12345678</p>";
    echo "<p style='color:#f87171;margin-top:20px;'>⚠️ Please delete this file (seed_user.php) from your server now!</p>";
    echo "<a href='index.php' style='display:inline-block;margin-top:15px;padding:10px 20px;background:#6366f1;color:white;text-decoration:none;border-radius:8px;'>Go to Login →</a>";
    echo "</div>";

} catch (PDOException $e) {
    echo "<p style='color:red;font-family:sans-serif;'>Error: " . $e->getMessage() . "</p>";
}
?>
