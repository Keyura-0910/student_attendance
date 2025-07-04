<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: ../auth/login.php");
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name         = $_POST['name'];
    $email        = $_POST['email'];
    $mobile       = $_POST['mobile'];
    $gender       = $_POST['gender'];
    $dob          = $_POST['dob'];
    $subjects     = $_POST['subjects'];
    $address      = $_POST['address'];
    $joining_date = $_POST['joining_date'];

    $photo = $_FILES['photo']['name'];
    $photo_tmp = $_FILES['photo']['tmp_name'];
    $target = '../uploads/staff/' . basename($photo);

    if (move_uploaded_file($photo_tmp, $target)) {
        $stmt = $pdo->prepare("INSERT INTO staff 
            (name, email, mobile, gender, dob, subjects, address, joining_date, photo, status, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())");

        $stmt->execute([
            $name, $email, $mobile, $gender, $dob, $subjects, $address, $joining_date, $photo
        ]);

        $success = " Staff registered successfully.";
    } else {
        $error = " Failed to upload photo.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Staff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/plugins/dropify/css/dropify.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include '../includes/navbar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow border-0">
                <div class="card-body">
                    <h4 class="text-center mb-4 text-primary"> Register New Staff</h4>

                    <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
                    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

                    <form method="POST" enctype="multipart/form-data" novalidate>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mobile Number</label>
                                <input type="text" name="mobile" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select" required>
                                    <option value="">Select</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="dob" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Subjects Handled</label>
                                <input type="text" name="subjects" class="form-control" required placeholder="e.g., Math, Science">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Joining Date</label>
                                <input type="date" name="joining_date" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Upload Photo</label>
                                <input type="file" name="photo" class="dropify" data-allowed-file-extensions="jpg jpeg png" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="2" required></textarea>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">📤 Register Staff</button>
                        </div>
                    </form>

                    <p class="text-center text-muted mt-3 small">Ensure photo is JPG or PNG under 2MB.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="../assets/js/jquery.min.js"></script>
<script src="../assets/plugins/dropify/js/dropify.min.js"></script>
<script>
    $('.dropify').dropify();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
