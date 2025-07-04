<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: ../auth/login.php");
require '../config/db.php';

// Fetch filters
$academic_years = $pdo->query("SELECT DISTINCT academic_year FROM students ORDER BY academic_year DESC")->fetchAll(PDO::FETCH_COLUMN); 
$classes = $pdo->query("SELECT id, class_name FROM classes WHERE status = 1")->fetchAll();
$students = $pdo->query("SELECT id, name FROM students WHERE status = 1")->fetchAll();

// Read filter values
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$class_id = $_GET['class_id'] ?? '';
$student_id = $_GET['student_id'] ?? '';
$academic_year = $_GET['academic_year'] ?? '';

// Build dynamic WHERE clause
$where = "WHERE 1=1";
$params = [];

if (!empty($from) && !empty($to)) {
    $where .= " AND a.date BETWEEN ? AND ?";
    $params[] = $from;
    $params[] = $to;
}
if (!empty($class_id)) {
    $where .= " AND c.id = ?";
    $params[] = $class_id;
}
if (!empty($student_id)) {
    $where .= " AND s.id = ?";
    $params[] = $student_id;
}
if (!empty($academic_year)) {
    $where .= " AND s.academic_year = ?";
    $params[] = $academic_year;
}

// Fetch records
$stmt = $pdo->prepare("
    SELECT a.date, s.name AS student_name, s.roll_number, c.class_name, a.status
    FROM attendance a
    JOIN students s ON a.student_id = s.id
    JOIN classes c ON a.class_id = c.id
    $where
    ORDER BY a.date DESC
");
$stmt->execute($params);
$records = $stmt->fetchAll();

// Excel export logic
if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=attendance_export_" . date('Y-m-d') . ".xls");
    echo "Date\tStudent Name\tRoll No\tClass\tStatus\n";
    foreach ($records as $r) {
        echo "{$r['date']}\t{$r['student_name']}\t{$r['roll_number']}\t{$r['class_name']}\t{$r['status']}\n";
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Export Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/plugins/datatables/datatables.min.css">
    <style>
        @media print {
            nav, .btn, .navbar, footer, form, .d-flex.justify-content-between { display: none !important; }
            .table { font-size: 14px; }
        }
    </style>
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="container py-4">
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="mb-3 text-primary">Filter Attendance</h4>
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from" class="form-control" value="<?= htmlspecialchars($from) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to" class="form-control" value="<?= htmlspecialchars($to) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Academic Year</label>
                    <select name="academic_year" class="form-select">
                        <option value="">All Years</option>
                        <?php foreach ($academic_years as $year): ?>
                            <option value="<?= $year ?>" <?= $academic_year == $year ? 'selected' : '' ?>><?= $year ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Class</label>
                    <select name="class_id" class="form-select">
                        <option value="">All Classes</option>
                        <?php foreach ($classes as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $class_id == $c['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['class_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Student</label>
                    <select name="student_id" class="form-select">
                        <option value="">All Students</option>
                        <?php foreach ($students as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= $student_id == $s['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-12 text-end">
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <h3 class="text-primary">Attendance Records</h3>
                <?php if (count($records) > 0): ?>
                    <div>
                        <a href="?<?= http_build_query($_GET + ['export' => 'excel']) ?>" class="btn btn-success me-2">Export Excel</a>
                        <button onclick="window.print()" class="btn btn-outline-secondary">🖨️ Print Report</button>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (count($records) > 0): ?>
                <div class="table-responsive">
                    <table id="attendanceTable" class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Date</th>
                                <th>Student Name</th>
                                <th>Roll No</th>
                                <th>Class</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($records as $r): ?>
                                <tr>
                                    <td><?= $r['date'] ?></td>
                                    <td><?= htmlspecialchars($r['student_name']) ?></td>
                                    <td><?= htmlspecialchars($r['roll_number']) ?></td>
                                    <td><?= htmlspecialchars($r['class_name']) ?></td>
                                    <td><?= $r['status'] === 'Present' ? 'Present' : 'Absent' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-warning text-center">
                    No attendance records found for selected filters.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="../assets/plugins/datatables/datatables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(() => {
        $('#attendanceTable').DataTable();
    });
</script>
</body>
</html>
