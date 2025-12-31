<?php
include 'db.php';
include 'header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$course = null;
$message = "";

// Fetch teachers for dropdown
$teachers_res = $conn->query("SELECT id, first_name, last_name FROM teachers");

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $id = intval($_POST['id']);
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $fees = floatval($_POST['fees']);
    $duration = $conn->real_escape_string($_POST['duration']);
    $teacher_id = intval($_POST['teacher_id']);

    $sql = "UPDATE courses SET name='$name', description='$description', fees='$fees', duration='$duration', teacher_id=$teacher_id WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "<script>window.location.href='courses.php';</script>";
        exit;
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// Fetch Course Data
if ($id > 0) {
    $result = $conn->query("SELECT * FROM courses WHERE id=$id");
    if ($result->num_rows > 0) {
        $course = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-warning'>Course not found.</div>";
    }
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <h4 class="card-title fw-bold mt-2">Edit Course</h4>
                </div>
                <div class="card-body">
                    <?php echo $message; ?>
                    <?php if ($course): ?>
                    <form method="POST" action="edit_course.php?id=<?php echo $id; ?>">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?php echo $course['id']; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Course Name</label>
                            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($course['name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2"><?php echo htmlspecialchars($course['description']); ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fees (LKR)</label>
                                <input type="number" step="0.01" class="form-control" name="fees" value="<?php echo htmlspecialchars($course['fees']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Duration</label>
                                <input type="text" class="form-control" name="duration" value="<?php echo htmlspecialchars($course['duration']); ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Assign Teacher</label>
                            <select class="form-select" name="teacher_id" required>
                                <option value="">Select a teacher...</option>
                                <?php 
                                if ($teachers_res && $teachers_res->num_rows > 0) {
                                    while($t = $teachers_res->fetch_assoc()) {
                                        $selected = ($t['id'] == $course['teacher_id']) ? 'selected' : '';
                                        echo "<option value='" . $t['id'] . "' $selected>" . htmlspecialchars($t['first_name'] . ' ' . $t['last_name']) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="courses.php" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Course</button>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
