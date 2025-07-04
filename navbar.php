<?php // includes/navbar.php ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="../admin/dashboard.php">Student Attendance</a>
    
    <!-- Toggle Button -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">

        <!-- Staff Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="javascript:void(0)" role="button" data-bs-toggle="dropdown">
            Staff
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="../admin/staff_register.php">Register Staff</a></li>
            <li><a class="dropdown-item" href="../admin/staff_view.php">View Staff</a></li>
          </ul>
        </li>

        <!-- Student Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="javascript:void(0)" role="button" data-bs-toggle="dropdown">
            Student
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="../admin/student_register.php">Register Student</a></li>
            <li><a class="dropdown-item" href="../admin/student_view.php">View Students</a></li>
          </ul>
        </li>

        <!-- Class Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="javascript:void(0)" role="button" data-bs-toggle="dropdown">
            Class
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="../admin/class_register.php">Register Class</a></li>
            <li><a class="dropdown-item" href="../admin/class_view.php">View Classes</a></li>
          </ul>
        </li>

        <!-- Assign Class -->
        <li class="nav-item">
          <a class="nav-link" href="../admin/assign_class.php">Assign Class</a>
        </li>

        <!-- Mark Attendance -->
        <li class="nav-item">
          <a class="nav-link" href="../admin/mark_attendance.php">Mark Attendance</a>
        </li>

        <!-- Export Attendance -->
        <li class="nav-item">
          <a class="nav-link" href="../admin/export_attendance.php">Export Attendance</a>
        </li>

        <!-- Summary -->
        <li class="nav-item">
          <a class="nav-link" href="../admin/summary.php">Summary</a>
        </li>

        <!-- Logout -->
        <li class="nav-item">
          <a class="nav-link" href="../auth/logout.php">Logout</a>
        </li>

      </ul>
    </div>
  </div>
</nav>

<!-- Bootstrap CSS + JS -->
<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
<script src="../assets/js/bootstrap.bundle.min.js"></script>
