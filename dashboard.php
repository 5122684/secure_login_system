<?php
require_once 'includes/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('index.php');
}

$username = $_SESSION['username'];
$email = $_SESSION['email'];

// Fetch login logs for this user
$stmt = $pdo->prepare("SELECT * FROM login_logs WHERE user_id = ? ORDER BY login_time DESC LIMIT 5");
$stmt->execute([$_SESSION['user_id']]);
$logs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Secure Login System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            display: block;
            background-attachment: fixed;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            padding: 30px;
            border-radius: 20px;
            text-align: center;
        }
        .log-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: var(--card-bg);
            border-radius: 16px;
            overflow: hidden;
        }
        .log-table th, .log-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        .status-badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .badge-success { background: rgba(34, 197, 94, 0.2); color: #4ade80; }
        .badge-failed { background: rgba(239, 68, 68, 0.2); color: #f87171; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <nav class="nav-glass">
            <h2 style="margin: 0; background: linear-gradient(to right, #818cf8, #c084fc); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">SecureAuth</h2>
            <div style="display: flex; align-items: center; gap: 20px;">
                <span style="color: var(--text-muted);">Welcome, <strong><?php echo $username; ?></strong></span>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </nav>

        <div class="stats-grid">
            <div class="stat-card">
                <h3 style="color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase;">User Profile</h3>
                <p style="font-size: 1.2rem; margin: 10px 0;"><?php echo $username; ?></p>
                <p style="color: var(--text-muted); font-size: 0.85rem;"><?php echo $email; ?></p>
            </div>
            <div class="stat-card">
                <h3 style="color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase;">Security Status</h3>
                <p style="font-size: 1.2rem; margin: 10px 0; color: var(--success);">Protected</p>
                <p style="color: var(--text-muted); font-size: 0.85rem;">Sessions are encrypted</p>
            </div>
        </div>

        <div class="auth-card" style="max-width: 100%;">
            <h3 style="margin-top: 0;">Recent Login Activity</h3>
            <table class="log-table">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>IP Address</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr><td colspan="3" style="text-align: center; color: var(--text-muted);">No activity logs found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo date('M d, Y H:i', strtotime($log['login_time'])); ?></td>
                            <td><?php echo $log['ip_address']; ?></td>
                            <td>
                                <span class="status-badge <?php echo $log['status'] === 'success' ? 'badge-success' : 'badge-failed'; ?>">
                                    <?php echo ucfirst($log['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
