<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include '../includes/navbar.php'; ?>

<div class="container py-5">
    <div class="text-center mb-4">
        <h1 class="display-6 text-primary"> Admin Dashboard</h1>
        <p class="lead">Use the menu above to manage staff, students, classes, attendance, and reports.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h4 class="mb-3">Welcome, Admin!</h4>
                    <p class="text-muted">You're logged in as an administrator. Navigate using the top navbar to manage the system modules.</p>
                    <a href="../auth/logout.php" class="btn btn-outline-danger mt-3">Logout</a> 
                    </div>
                    
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS CDN (optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
