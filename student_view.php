<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: ../auth/login.php");
require '../config/db.php';

// Get all distinct academic years for filter dropdown
$years_stmt = $pdo->query("SELECT DISTINCT academic_year FROM students ORDER BY academic_year DESC");
$academic_years = $years_stmt->fetchAll(PDO::FETCH_COLUMN);

// Handle filter
$selected_year = $_GET['academic_year'] ?? '';
$sql = "SELECT students.*, classes.class_name 
        FROM students 
        JOIN classes ON students.class_id = classes.id 
        WHERE students.status = 1";

if ($selected_year !== '') {
    $sql .= " AND students.academic_year = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$selected_year]);
} else {
    $stmt = $pdo->query($sql);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Full Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="../admin/dashboard.php">Student Attendance</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Staff</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="../admin/staff_register.php">Register Staff</a></li>
            <li><a class="dropdown-item" href="../admin/staff_view.php">View Staff</a></li>
          </ul>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Student</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="../admin/student_register.php">Register Student</a></li>
            <li><a class="dropdown-item" href="../admin/student_view.php">View Students</a></li>
          </ul>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Class</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="../admin/class_register.php">Register Class</a></li>
            <li><a class="dropdown-item" href="../admin/class_view.php">View Classes</a></li>
          </ul>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="../admin/assign_class.php">Assign Class</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../admin/mark_attendance.php">Mark Attendance</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../admin/summary.php">Summary</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../admin/export_attendance.php">Export Attendance</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../auth/logout.php">Logout</a>
        </li>

      </ul>
    </div>
  </div>
</nav>

<!-- Content -->
<div class="container py-5">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h4 class="mb-4 text-center text-primary"> Registered Students</h4>

            <form method="GET" class="mb-3">
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <label for="academic_year" class="col-form-label"> Filter by Academic Year:</label>
                    </div>
                    <div class="col-auto">
                        <select name="academic_year" id="academic_year" class="form-select" onchange="this.form.submit()">
                            <option value="">-- All Years --</option>
                            <?php foreach ($academic_years as $year): ?>
                                <option value="<?= $year ?>" <?= $year === $selected_year ? 'selected' : '' ?>>
                                    <?= $year ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </form>

            <table id="studentTable" class="table table-bordered table-striped table-hover align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>ID</th>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Roll No</th>
                        <th>Class</th>
                        <th>Academic Year</th>
                        <th>Registered At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $stmt->fetch()) { ?>
                        <tr class="text-center">
                            <td><?= $row['id'] ?></td>
                            <td><img src="../uploads/students/<?= $row['photo'] ?>" width="50" height="50" class="rounded-circle"></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['roll_number']) ?></td>
                            <td><?= htmlspecialchars($row['class_name']) ?></td>
                            <td><?= htmlspecialchars($row['academic_year']) ?></td>
                            <td><?= date("d M Y, h:i A", strtotime($row['created_at'])) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function () {
        $('#studentTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
        });
    });
</script>
</body>
</html>
