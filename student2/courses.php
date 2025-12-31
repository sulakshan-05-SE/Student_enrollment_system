<?php
include 'db.php';

$message = "";

// Handle Add Course
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $fees = floatval($_POST['fees']);
    $duration = $conn->real_escape_string($_POST['duration']);
    $teacher_id = intval($_POST['teacher_id']);

    $sql = "INSERT INTO courses (name, description, fees, duration, teacher_id) VALUES ('$name', '$description', '$fees', '$duration', $teacher_id)";
    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-success'>Course created successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM courses WHERE id=$id");
    header("Location: courses.php");
    exit;
}

// Fetch teachers for dropdown
$teachers_res = $conn->query("SELECT id, first_name, last_name FROM teachers");

include 'header.php';
?>

<div class="page-header">
    <h2 class="page-title">Courses</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
        <i class="fas fa-plus me-2"></i>Create Course
    </button>
</div>

<?php echo $message; ?>

<div class="card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table-custom mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Course Name</th>
                    <th>Description</th>
                    <th>Fees</th>
                    <th>Duration</th>
                    <th>Assigned Teacher</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT courses.*, CONCAT(teachers.first_name, ' ', teachers.last_name) as teacher_name 
                        FROM courses 
                        LEFT JOIN teachers ON courses.teacher_id = teachers.id 
                        ORDER BY courses.id DESC";
                $result = $conn->query($sql);
                
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>#" . $row['id'] . "</td>";
                        echo "<td class='fw-bold'>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                        echo "<td>LKR " . number_format($row['fees'], 2) . "</td>";
                        echo "<td>" . htmlspecialchars($row['duration']) . "</td>";
                        echo "<td><span class='badge bg-light text-dark border'>" . ($row['teacher_name'] ? htmlspecialchars($row['teacher_name']) : 'Unassigned') . "</span></td>";
                        echo "<td class='text-end'>
                                <a href='edit_course.php?id=" . $row['id'] . "' class='btn btn-light btn-sm text-primary me-1'><i class='fas fa-edit'></i></a>
                                <a href='courses.php?delete=" . $row['id'] . "' class='btn btn-light btn-sm text-danger' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i></a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center py-4'>No courses found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Course Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Create New Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="courses.php">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">Course Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Fees (LKR)</label>
                            <input type="number" step="0.01" class="form-control" name="fees" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Duration</label>
                            <input type="text" class="form-control" name="duration" placeholder="e.g. 3 Months" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assign Teacher</label>
                        <select class="form-select" name="teacher_id" required>
                            <option value="">Select a teacher...</option>
                            <?php 
                            if ($teachers_res && $teachers_res->num_rows > 0) {
                                while($t = $teachers_res->fetch_assoc()) {
                                    echo "<option value='" . $t['id'] . "'>" . htmlspecialchars($t['first_name'] . ' ' . $t['last_name']) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Create Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
