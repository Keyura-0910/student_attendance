<?php
session_start();
date_default_timezone_set('Asia/Kolkata');      // ← add this line
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit;
}
require '../config/db.php';

// Get admin name
$stmtAdmin = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmtAdmin->execute([$_SESSION['admin']]);
$admin = $stmtAdmin->fetch();
$adminName = $admin ? ucfirst($admin['username']) : "Admin";

// Time-based greeting
$hour = (int)date('H');
if ($hour >= 5 && $hour < 12) $greeting = "Good Morning";
elseif ($hour >= 12 && $hour < 17) $greeting = "Good Afternoon";
elseif ($hour >= 17 && $hour < 21) $greeting = "Good Evening";
else $greeting = "Good Night";

// Get classes
$class_stmt = $pdo->query("SELECT * FROM classes WHERE status = 1");
$classes = $class_stmt->fetchAll();

// Filter inputs
$filter_date  = $_GET['date']     ?? date('Y-m-d');
$filter_class = $_GET['class_id'] ?? '';

// Attendance summary query
$params = [$filter_date];
$sql = "SELECT 
            SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS present,
            SUM(CASE WHEN a.status = 'Absent'  THEN 1 ELSE 0 END) AS absent
        FROM attendance a
        JOIN students s ON a.student_id = s.id
        WHERE a.date = ?";
if ($filter_class) {
    $sql .= " AND s.class_id = ?";
    $params[] = $filter_class;
}
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$att = $stmt->fetch();

// Summary counts
$total_students = $pdo->query("SELECT COUNT(*) FROM students WHERE status = 1")->fetchColumn();
$total_staff    = $pdo->query("SELECT COUNT(*) FROM staff    WHERE status = 1")->fetchColumn();
$total_classes  = $pdo->query("SELECT COUNT(*) FROM classes   WHERE status = 1")->fetchColumn();

// Chart data (unfiltered)
$class_data    = $pdo->query("
    SELECT c.class_name, COUNT(s.id) AS total_students
    FROM classes c
    LEFT JOIN students s 
      ON c.id = s.class_id 
     AND s.status = 1
    WHERE c.status = 1
    GROUP BY c.id
")->fetchAll();
$class_labels = array_column($class_data, 'class_name');
$class_counts = array_column($class_data, 'total_students');

// Export to Excel
if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=attendance_summary_{$filter_date}.xls");
    echo "Date\tClass\tPresent\tAbsent\n";
    echo "{$filter_date}\t" . ($filter_class ?: 'All') . "\t{$att['present']}\t{$att['absent']}";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>System Summary</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
      @media print {
        nav, .btn, .navbar, footer { display: none !important; }
      }
    </style>
</head>
<body class="bg-light">

<?php include '../includes/navbar.php'; ?>

<div class="container py-4">
  <div class="text-center mb-4">
    <h3 class="text-primary fw-bold"> System Summary Dashboard</h3>
    <p class="lead"><?= $greeting ?>, <strong><?= htmlspecialchars($adminName) ?></strong>!</p>
    <p class="text-muted">Generated on <?= date("F j, Y, g:i a") ?></p>
    <a href="?date=<?= $filter_date ?>&class_id=<?= $filter_class ?>&export=excel"
       class="btn btn-success me-2"> Export to Excel</a>
    <button onclick="window.print()" class="btn btn-outline-secondary">🖨️ Print Report</button>
  </div>

  <!-- Summary Cards -->
  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="card shadow text-white bg-info h-100">
        <div class="card-body text-center">
          <h5 class="card-title"> Students</h5>
          <h2><?= $total_students ?></h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow text-white bg-primary h-100">
        <div class="card-body text-center">
          <h5 class="card-title"> Staff</h5>
          <h2><?= $total_staff ?></h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow text-dark bg-warning h-100">
        <div class="card-body text-center">
          <h5 class="card-title"> Classes</h5>
          <h2><?= $total_classes ?></h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow text-white bg-success h-100">
        <div class="card-body text-center">
          <h5 class="card-title">
             Attendance on <?= htmlspecialchars($filter_date) ?>
          </h5>
          <h6> Present: <?= $att['present'] ?? 0 ?></h6>
          <h6> Absent: <?= $att['absent'] ?? 0 ?></h6>
          <?php if ($filter_class): ?>
            <small>Class:
              <strong>
                <?= htmlspecialchars(
                     array_values(
                       array_filter($classes, fn($c)=>$c['id']==$filter_class)
                     )[0]['class_name'] ?? ''
                   ) ?>
              </strong>
            </small>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters -->
  <form method="GET" class="row g-3 mb-4 align-items-end">
    <div class="col-md-4">
      <label for="date" class="form-label">Select Date</label>
      <input type="date" id="date" name="date"
             class="form-control" value="<?= htmlspecialchars($filter_date) ?>" required>
    </div>
    <div class="col-md-4">
      <label for="class_id" class="form-label">Select Class (optional)</label>
      <select id="class_id" name="class_id"
              class="form-select select2">
        <option value="">All Classes</option>
        <?php foreach ($classes as $c): ?>
          <option value="<?= $c['id'] ?>"
            <?= ($c['id']==$filter_class)?'selected':''?>>
            <?= htmlspecialchars($c['class_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4">
      <button type="submit" class="btn btn-primary w-100">Filter</button>
    </div>
  </form>

  <!-- Charts -->
  <div class="row g-4">
    <div class="col-md-6">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-title text-center text-secondary mb-3">
             Attendance Breakdown
          </h5>
          <canvas id="attendancePie" height="300"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-title text-center text-secondary mb-3">
             Students Per Class
          </h5>
          <canvas id="studentBar" height="300"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="../assets/plugins/select2/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  $(document).ready(function(){
    $('#class_id').select2({
      theme: 'bootstrap-5',
      width: '100%'
    });
    // Auto-refresh every 60 seconds
    setInterval(() => location.reload(), 60000);
  });
</script>
<script>
  // Render Chart.js charts after page load
  const ctxPie = document.getElementById('attendancePie').getContext('2d');
  new Chart(ctxPie, {
    type: 'pie',
    data: {
      labels: ['Present','Absent'],
      datasets: [{
        data: [<?= $att['present'] ?? 0 ?>,<?= $att['absent'] ?? 0 ?>],
        backgroundColor:['#198754','#dc3545']
      }]
    },
    options:{
      responsive:true,
      plugins:{ legend:{ position:'bottom', labels:{ boxWidth:20, padding:15 } } }
    }
  });

  const ctxBar = document.getElementById('studentBar').getContext('2d');
  new Chart(ctxBar, {
    type: 'bar',
    data: {
      labels: <?= json_encode($class_labels) ?>,
      datasets:[{
        label:'Students',
        data: <?= json_encode($class_counts) ?>,
        backgroundColor:'#0d6efd'
      }]
    },
    options:{
      responsive:true,
      scales:{ y:{ beginAtZero:true, stepSize:1 } },
      plugins:{ legend:{ display:false } }
    }
  });
</script>
</body>
</html>
