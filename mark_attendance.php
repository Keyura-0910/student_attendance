<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: ../auth/login.php");
require '../config/db.php';

// Fetch academic years from students table
$year_stmt = $pdo->query("SELECT DISTINCT academic_year FROM students ORDER BY academic_year DESC");
$academic_years = $year_stmt->fetchAll(PDO::FETCH_COLUMN);

// Get selected academic year and class from POST
$selected_year = $_POST['academic_year'] ?? '';
$selected_class_id = $_POST['class_id'] ?? '';
$marked = false;

// Fetch classes based on selected academic year
$classes = [];
if (!empty($selected_year)) {
    $class_stmt = $pdo->prepare("SELECT * FROM classes WHERE academic_year = ? AND status = 1");
    $class_stmt->execute([$selected_year]);
    $classes = $class_stmt->fetchAll();
}

// Handle attendance submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_attendance'])) {
    $class_id = $_POST['class_id'];
    $academic_year = $_POST['academic_year'];
    $date = date('Y-m-d');
    $statuses = $_POST['status'];

    foreach ($statuses as $student_id => $status) {
        $stmt = $pdo->prepare("INSERT INTO attendance (student_id, class_id, academic_year, date, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, NOW(), NOW()) 
            ON DUPLICATE KEY UPDATE status = VALUES(status), updated_at = NOW()");
        $stmt->execute([$student_id, $class_id, $academic_year, $date, $status]);
    }
    $marked = true;
}

// Fetch students for selected class and academic year
$students = [];
if ($selected_class_id && $selected_year) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE class_id = ? AND academic_year = ? AND status = 1");
    $stmt->execute([$selected_class_id, $selected_year]);
    $students = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mark Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/plugins/select2/select2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<?php include '../includes/navbar.php'; ?>

<div class="container py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h4 class="mb-4 text-primary text-center"> Mark Attendance</h4>

            <?php if ($marked): ?>
                <div class="alert alert-success text-center"> Attendance marked successfully!</div>
            <?php endif; ?>

            <!-- Academic Year & Class Selection Form -->
            <form method="POST" class="mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label">Academic Year</label>
                        <select name="academic_year" class="form-select select2" onchange="this.form.submit()" required>
                            <option value="">-- Select Academic Year --</option>
                            <?php foreach ($academic_years as $year): ?>
                                <option value="<?= $year ?>" <?= ($year == $selected_year) ? 'selected' : '' ?>><?= $year ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Select Class</label>
                        <select name="class_id" class="form-select select2" onchange="this.form.submit()" required>
                            <option value="">-- Select Class --</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?= $class['id'] ?>" <?= ($class['id'] == $selected_class_id) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($class['class_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </form>

            <?php if ($selected_class_id && count($students) > 0): ?>
                <!-- Attendance Marking Form -->
                <form method="POST">
                    <input type="hidden" name="class_id" value="<?= $selected_class_id ?>">
                    <input type="hidden" name="academic_year" value="<?= $selected_year ?>">
                    <input type="hidden" name="mark_attendance" value="1">

                    <div class="table-responsive mt-4">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Roll No</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><img src="../uploads/students/<?= $student['photo'] ?>" width="50" class="rounded-circle"></td>
                                        <td><?= htmlspecialchars($student['name']) ?></td>
                                        <td><?= htmlspecialchars($student['roll_number']) ?></td>
                                        <td>
                                            <select name="status[<?= $student['id'] ?>]" class="form-select">
                                                <option value="Present">Present</option>
                                                <option value="Absent">Absent</option>
                                            </select>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-success px-4"> Submit Attendance</button>
                    </div>
                </form>
            <?php elseif ($selected_class_id && empty($students)): ?>
                <div class="alert alert-warning mt-4 text-center">No students found in this class and academic year.</div>
            <?php endif; ?>
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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="../assets/plugins/select2/select2.min.js"></script>
<script>
    $(document).ready(() => {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    });
</script>
</body>
</html>
