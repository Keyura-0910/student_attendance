<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: ../auth/login.php");
require '../config/db.php';

$class_stmt = $pdo->query("SELECT * FROM classes WHERE status = 1");
$classes = $class_stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $roll_number = $_POST['roll_number'];
    $class_id = $_POST['class_id'];
    $academic_year = $_POST['academic_year'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $admission_date = $_POST['admission_date'];

    $photo = $_FILES['photo']['name'];
    $photo_tmp = $_FILES['photo']['tmp_name'];
    $target = '../uploads/students/' . basename($photo);

    if (move_uploaded_file($photo_tmp, $target)) {
        $stmt = $pdo->prepare("INSERT INTO students 
            (name, roll_number, class_id, academic_year, gender, dob, address, admission_date, photo, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())");

        $stmt->execute([$name, $roll_number, $class_id, $academic_year, $gender, $dob, $address, $admission_date, $photo]);
        $success = " Student registered successfully.";
    } else {
        $error = " Failed to upload photo.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/plugins/dropify/css/dropify.min.css">
    <link rel="stylesheet" href="../assets/plugins/select2/select2.min.css">
</head>
<body class="bg-light">

<?php include '../includes/navbar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow border-0">
                <div class="card-body">
                    <h4 class="mb-4 text-center text-primary">📚 Register New Student</h4>

                    <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
                    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

                    <form method="POST" enctype="multipart/form-data" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Roll Number</label>
                            <input type="text" name="roll_number" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Academic Year</label>
                            <input type="text" name="academic_year" class="form-control" required placeholder="e.g., 2024-25">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select" required>
                                <option value="">-- Select Gender --</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="dob" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Class</label>
                            <select name="class_id" class="form-select select2" required>
                                <option value="">-- Select Class --</option>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['class_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Admission Date</label>
                            <input type="date" name="admission_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" rows="2" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Upload Photo</label>
                            <input type="file" name="photo" class="dropify" data-allowed-file-extensions="jpg jpeg png" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">📥 Register Student</button>
                        </div>
                    </form>

                    <p class="text-center text-muted mt-3 small">Upload a clear JPG or PNG photo under 2MB.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!--  Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/plugins/dropify/js/dropify.min.js"></script>
<script src="../assets/plugins/select2/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.dropify').dropify();
        $('.select2').select2({ width: '100%' });
    });
</script>
</body>
</html>
