<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit;
}

require '../config/db.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $class_id = (int)$_GET['id'];

    // Soft delete: Set status = 0
    $stmt = $pdo->prepare("UPDATE classes SET status = 0, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$class_id]);
}

header("Location: class_view.php");
exit;
