<?php
// 1. Include the secure database connection file
require 'db.php';

$message = "";
$users = [];

// 2. Automatically create the table if it does not exist yet
try {
    $createTableSql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );";
    $pdo->exec($createTableSql);
} catch (\PDOException $e) {
    $message = "Table setup failed: " . $e->getMessage();
}

// 3. Handle Form Submission (Adding a User)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (!empty($username) && !empty($email)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            try {
                // Prepared statement to prevent SQL Injection
                $stmt = $pdo->prepare('INSERT INTO users (username, email) VALUES (?, ?)');
                $stmt->execute([$username, $email]);
                $message = "User added successfully!";
            } catch (\PDOException $e) {
                // Handle duplicate email error smoothly
                if ($e->getCode() == 23000) {
                    $message = "Error: This email is already registered.";
                } else {
                    $message = "Database error: " . $e->getMessage();
                }
            }
        } else {
            $message = "Please provide a valid email address.";
        }
    } else {
        $message = "All fields are required.";
    }
}

// 4. Securely fetch all users to display
try {
    $stmt = $pdo->query('SELECT username, email, created_at FROM users ORDER BY id DESC');
    $users = $stmt->fetchAll();
} catch (\PDOException $e) {
    $message = "Failed to fetch users: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP & MariaDB Deployment Test</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background-color: #f4f7f6; color: #333; margin: 40px auto; max-width: 600px; padding: 0 20px; }
        .card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 20px; }
        h1, h2 { color: #2c3e50; margin-top: 0; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #2ecc71; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; font-size: 16px; width: 100%; }
        button:hover { background-color: #27ae60; }
        .alert { padding: 10px; background-color: #3498db; color: white; border-radius: 4px; margin-bottom: 15px; }
        ul { list-style: none; padding: 0; }
        li { background: #f9f9f9; padding: 10px; margin-bottom: 8px; border-left: 4px solid #3498db; border-radius: 0 4px 4px 0; }
        .meta { font-size: 12px; color: #7f8c8d; }
    </style>
</head>
<body>

    <h1>🚀 Production Test App</h1>
    <p>Testing PHP connection to MariaDB via Render deployment.</p>

    <!-- Notification Area -->
    <?php if (!empty($message)): ?>
        <div class="alert"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- User Entry Form -->
    <div class="card">
        <h2>Add Live User</h2>
        <form method="POST" action="index.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="e.g. JohnDoe" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="e.g. john@example.com" required>
            </div>
            <button type="submit">Save to Database</button>
        </form>
    </div>

    <!-- Active Directory Display -->
    <div class="card">
        <h2>Registered Users</h2>
        <?php if (empty($users)): ?>
            <p style="color: #7f8c8d;">No users found. Try adding the first one above!</p>
        <?php else: ?>
            <ul>
                <?php foreach ($users as $user): ?>
                    <li>
                        <strong><?= htmlspecialchars($user['username']) ?></strong> 
                        (<span class="meta"><?= htmlspecialchars($user['email']) ?></span>)
                        <br>
                        <span class="meta">Added: <?= htmlspecialchars($user['created_at']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

</body>
</html>
