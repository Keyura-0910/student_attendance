<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: ../auth/login.php");
require '../config/db.php';

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_name = trim($_POST['class_name']);
    $academic_year = trim($_POST['academic_year']);

    if (empty($class_name) || empty($academic_year)) {
        $error = " Class name and academic year are required.";
    } else {
        // Check for duplicate class name in same academic year
        $check = $pdo->prepare("SELECT COUNT(*) FROM classes WHERE LOWER(class_name) = LOWER(?) AND academic_year = ? AND status = 1");
        $check->execute([$class_name, $academic_year]);
        if ($check->fetchColumn() > 0) {
            $error = "Class '$class_name' already exists for $academic_year.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO classes (class_name, academic_year, status, created_at, updated_at) 
                                   VALUES (?, ?, 1, NOW(), NOW())");
            $stmt->execute([$class_name, $academic_year]);
            $success = "Class '$class_name' registered successfully for $academic_year.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Class</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include '../includes/navbar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h4 class="mb-4 text-primary text-center"> Register New Class</h4>

                    <?php if ($success): ?>
                        <div class="alert alert-success text-center"><?= $success ?></div>
                    <?php elseif ($error): ?>
                        <div class="alert alert-danger text-center"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST" autocomplete="off">
                        <div class="mb-3">
                            <label class="form-label">Class Name <small class="text-muted">(e.g., 1A, 2B)</small></label>
                            <input type="text" name="class_name" class="form-control" required placeholder="Enter class name">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Academic Year <small class="text-muted">(e.g., 2024-2025)</small></label>
                            <input type="text" name="academic_year" class="form-control" required placeholder="Enter academic year">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary"> Register Class</button>
                        </div>
                    </form>

                    <p class="text-center text-muted mt-3 small">Each class must be unique per academic year.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- Responsive extension -->
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
    $(document).ready(function () {
        $('#staffTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
        });
    });
</script>
</body>
</html>
