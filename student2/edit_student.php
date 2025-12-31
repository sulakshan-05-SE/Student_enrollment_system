<?php
include 'db.php';
include 'header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$student = null;
$message = "";

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $id = intval($_POST['id']);
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $dob = $conn->real_escape_string($_POST['dob']);
    $age = intval($_POST['age']);
    $address = $conn->real_escape_string($_POST['address']);

    $sql = "UPDATE students SET first_name='$first_name', last_name='$last_name', email='$email', dob='$dob', age=$age, address='$address' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "<script>window.location.href='students.php';</script>";
        exit;
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// Fetch Student Data
if ($id > 0) {
    $result = $conn->query("SELECT * FROM students WHERE id=$id");
    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-warning'>Student not found.</div>";
    }
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <h4 class="card-title fw-bold mt-2">Edit Student</h4>
                </div>
                <div class="card-body">
                    <?php echo $message; ?>
                    <?php if ($student): ?>
                    <form method="POST" action="edit_student.php?id=<?php echo $id; ?>">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" name="dob" value="<?php echo $student['dob']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Age</label>
                                <input type="number" class="form-control" name="age" value="<?php echo htmlspecialchars($student['age']); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="2" required><?php echo htmlspecialchars($student['address']); ?></textarea>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="students.php" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Student</button>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
