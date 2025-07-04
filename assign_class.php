<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: ../auth/login.php");
require '../config/db.php';

// Fetch staff and classes
$staffs = $pdo->query("SELECT id, name FROM staff WHERE status = 1")->fetchAll();
$classes = $pdo->query("SELECT id, class_name FROM classes WHERE status = 1")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staff_id = $_POST['staff_id'];
    $class_id = $_POST['class_id'];

    // Check for duplicate assignment
    $stmt = $pdo->prepare("SELECT * FROM class_teacher_allotment WHERE staff_id = ? AND class_id = ?");
    $stmt->execute([$staff_id, $class_id]);

    if ($stmt->rowCount() === 0) {
        $stmt = $pdo->prepare("INSERT INTO class_teacher_allotment (staff_id, class_id, status, created_at, updated_at)
            VALUES (?, ?, 1, NOW(), NOW())");
        $stmt->execute([$staff_id, $class_id]);
        $success = " Class successfully assigned to teacher.";
    } else {
        $error = " This class is already assigned to this teacher.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Class to Teacher</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include '../includes/navbar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow border-0">
                <div class="card-body">
                    <h4 class="text-center mb-4 text-primary"> Assign Class to Teacher</h4>

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php elseif (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Select Teacher</label>
                            <select name="staff_id" class="form-select select2" required>
                                <option value="">-- Select Teacher --</option>
                                <?php foreach ($staffs as $staff): ?>
                                    <option value="<?= $staff['id'] ?>"><?= htmlspecialchars($staff['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Select Class</label>
                            <select name="class_id" class="form-select select2" required>
                                <option value="">-- Select Class --</option>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['class_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary px-4"> Assign</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/plugins/select2/select2.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        $('.select2').select2({
            theme: 'bootstrap5',
            width: '100%'
        });
    });
</script>
</body>
</html>
