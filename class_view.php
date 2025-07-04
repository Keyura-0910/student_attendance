<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: ../auth/login.php");
require '../config/db.php';

$sql = "
    SELECT c.*, 
           s.name AS teacher_name,
           (SELECT COUNT(*) FROM students WHERE class_id = c.id AND status = 1) AS total_students
    FROM classes c
    LEFT JOIN class_teacher_allotment a ON a.class_id = c.id AND a.status = 1
    LEFT JOIN staff s ON a.staff_id = s.id
";
$stmt = $pdo->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Classes</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include '../includes/navbar.php'; ?>

<div class="container py-5">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h4 class="mb-4 text-center text-primary"> Class List</h4>

            <div class="table-responsive">
                <table id="classTable" class="table table-bordered table-hover align-middle nowrap" style="width:100%">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>ID</th>
                            <th>Class Name</th>
                            <th>Class Teacher</th>
                            <th>Total Students</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $stmt->fetch()) { ?>
                            <tr class="text-center">
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['class_name']) ?></td>
                                <td><?= $row['teacher_name'] ? htmlspecialchars($row['teacher_name']) : '<em class="text-muted">Not Assigned</em>' ?></td>
                                <td><?= $row['total_students'] ?></td>
                                <td>
                                    <span class="badge <?= $row['status'] ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $row['status'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td><?= date("d M Y, h:i A", strtotime($row['created_at'])) ?></td>
                                <td><?= date("d M Y, h:i A", strtotime($row['updated_at'])) ?></td>
                                <td>
                                    <a href="toggle_class_status.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-warning">Toggle</a>
                                    <a href="delete_class.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this class?');">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
    $(document).ready(function () {
        $('#classTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            dom: 'Bfrtip',
            buttons: [
                { extend: 'excelHtml5', className: 'btn btn-success btn-sm', title: 'Class List' },
                { extend: 'pdfHtml5', className: 'btn btn-danger btn-sm', title: 'Class List' },
                { extend: 'print', className: 'btn btn-secondary btn-sm', title: 'Class List' }
            ]
        });
    });
</script>
</body>
</html>
