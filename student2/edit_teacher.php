<?php
include 'db.php';
include 'header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$teacher = null;
$message = "";

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $id = intval($_POST['id']);
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);

    $sql = "UPDATE teachers SET first_name='$first_name', last_name='$last_name', email='$email', phone='$phone' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "<script>window.location.href='teachers.php';</script>";
        exit;
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// Fetch Teacher Data
if ($id > 0) {
    $result = $conn->query("SELECT * FROM teachers WHERE id=$id");
    if ($result->num_rows > 0) {
        $teacher = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-warning'>Teacher not found.</div>";
    }
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <h4 class="card-title fw-bold mt-2">Edit Teacher</h4>
                </div>
                <div class="card-body">
                    <?php echo $message; ?>
                    <?php if ($teacher): ?>
                    <form method="POST" action="edit_teacher.php?id=<?php echo $id; ?>">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?php echo $teacher['id']; ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($teacher['first_name']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($teacher['last_name']); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($teacher['email']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($teacher['phone']); ?>">
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="teachers.php" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Teacher</button>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
