<?php
session_start();
if (isset($_SESSION['admin'])) {
    header("Location: admin/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome | Student Attendance Management System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #007bff, #6610f2);
      color: white;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', sans-serif;
    }
    .card {
      background: white;
      color: #333;
      padding: 2rem;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      text-align: center;
      max-width: 450px;
    }
    .card h1 {
      font-size: 2rem;
      margin-bottom: 1rem;
      color: #6610f2;
    }
    .card p {
      font-size: 1.1rem;
      margin-bottom: 2rem;
    }
  </style>
</head>
<body>

<div class="card">
  <h1>Welcome to</h1>
  <h2 class="fw-bold">Student Attendance Management System</h2>
  <p class="mt-3">Manage students, staff, classes, and attendance easily with this portal.</p>
  <a href="auth/login.php" class="btn btn-primary btn-lg">Login as Admin</a>
</div>

</body>
</html>
