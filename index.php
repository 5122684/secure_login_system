<?php
require_once 'includes/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF Token
    verifyCSRF($_POST['csrf_token'] ?? '');

    $login_input = cleanInput($_POST['login_input'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($login_input) || empty($password)) {
        $error = "Please enter both username/email and password.";
    } else {
        // Find user by username or email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$login_input, $login_input]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Success! Start Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];

            // Log selection (Phase 3 sneak peek)
            $log_stmt = $pdo->prepare("INSERT INTO login_logs (user_id, ip_address, status) VALUES (?, ?, 'success')");
            $log_stmt->execute([$user['id'], $_SERVER['REMOTE_ADDR']]);

            redirect('dashboard.php');
        } else {
            $error = "Invalid username/email or password.";
            
            // Log failure if user exists
            if ($user) {
                $log_stmt = $pdo->prepare("INSERT INTO login_logs (user_id, ip_address, status) VALUES (?, ?, 'failed')");
                $log_stmt->execute([$user['id'], $_SERVER['REMOTE_ADDR']]);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Secure Login System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Welcome Back</h1>
                <p>Sign in to your secure dashboard</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="index.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="form-group">
                    <label class="form-label">Username or Email</label>
                    <input type="text" name="login_input" class="form-control" placeholder="Enter username or email" required value="<?php echo isset($login_input) ? $login_input : ''; ?>">
                </div>

                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <label class="form-label">Password</label>
                        <a href="#" style="font-size: 0.8rem; color: var(--primary-color); text-decoration: none; margin-bottom: 8px;">Forgot?</a>
                    </div>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

                <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" id="remember" name="remember" style="accent-color: var(--primary-color);">
                    <label for="remember" style="font-size: 0.85rem; color: var(--text-muted); cursor: pointer;">Remember me</label>
                </div>

                <button type="submit" class="btn-primary">Sign In</button>
            </form>

            <div class="auth-footer">
                Don't have an account? <a href="register.php">Create one</a>
            </div>
        </div>
    </div>
</body>
</html>
