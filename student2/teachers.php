<?php
include 'db.php';

$message = "";

// Handle Add Teacher
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);

    $sql = "INSERT INTO teachers (first_name, last_name, email, phone) VALUES ('$first_name', '$last_name', '$email', '$phone')";
    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-success'>Teacher added successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// Handle Delete Teacher
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Check if handling courses
    $check = $conn->query("SELECT count(*) as count FROM courses WHERE teacher_id=$id");
    if($check->fetch_assoc()['count'] > 0){
         $message = "<div class='alert alert-warning'>Cannot delete teacher. They are assigned to courses. Reassign courses first.</div>";
    } else {
        $conn->query("DELETE FROM teachers WHERE id=$id");
        header("Location: teachers.php");
        exit;
    }
}

include 'header.php';
?>

<div class="page-header">
    <h2 class="page-title">Teachers</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
        <i class="fas fa-plus me-2"></i>Add Teacher
    </button>
</div>

<?php echo $message; ?>

<div class="card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table-custom mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Assigned Courses</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // We use GROUP_CONCAT to show multiple courses if any (though currently schema allows 1 teacher per course, query handles if we show list)
                // Actually, a teacher can have multiple courses, so GROUP_CONCAT(courses.name) works perfectly here.
                $sql = "SELECT t.*, GROUP_CONCAT(c.name SEPARATOR ', ') as course_names 
                        FROM teachers t 
                        LEFT JOIN courses c ON t.id = c.teacher_id 
                        GROUP BY t.id 
                        ORDER BY t.id DESC";
                $result = $conn->query($sql);
                
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $fullName = $row['first_name'] . ' ' . $row['last_name'];
                        echo "<tr>";
                        echo "<td>#" . $row['id'] . "</td>";
                        echo "<td class='fw-bold'>" . htmlspecialchars($fullName) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                        echo "<td>" . ($row['course_names'] ? '<span class="badge bg-info text-dark">' . htmlspecialchars($row['course_names']) . '</span>' : '<span class="text-muted small">None</span>') . "</td>";
                        echo "<td class='text-end'>
                                <a href='edit_teacher.php?id=" . $row['id'] . "' class='btn btn-light btn-sm text-primary me-1'><i class='fas fa-edit'></i></a>
                                <a href='teachers.php?delete=" . $row['id'] . "' class='btn btn-light btn-sm text-danger' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i></a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center py-4'>No teachers found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Teacher Modal -->
<div class="modal fade" id="addTeacherModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Add New Teacher</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="teachers.php">
                    <input type="hidden" name="action" value="add">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="first_name" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" class="form-control" name="phone">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Save Teacher</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
