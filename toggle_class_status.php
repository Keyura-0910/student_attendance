<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit;
}

require '../config/db.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $class_id = (int)$_GET['id'];

    // Fetch current status of the class
    $stmt = $pdo->prepare("SELECT status FROM classes WHERE id = ?");
    $stmt->execute([$class_id]);
    $current_status = $stmt->fetchColumn();

    if ($current_status !== false) {
        // Toggle status: 1 -> 0, 0 -> 1
        $new_status = $current_status == 1 ? 0 : 1;

        $update = $pdo->prepare("UPDATE classes SET status = ?, updated_at = NOW() WHERE id = ?");
        $update->execute([$new_status, $class_id]);
    }
}

// Redirect back to class list
header("Location: class_view.php");
exit;
