<?php
require '../config/db.php';

$username = 'admin';
$password = 'admin123';
$role = 'admin';

// Check if admin already exists
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
$stmt->execute([$username]);
if ($stmt->fetchColumn() > 0) {
    echo "⚠️ Admin user already exists. No action taken.";
    exit;
}

// If not, insert new admin
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (username, password, role, status, created_at, updated_at)
VALUES (?, ?, ?, 1, NOW(), NOW())");
$stmt->execute([$username, $hashed_password, $role]);

echo "✅ Admin user created. Username: admin, Password: admin123";
