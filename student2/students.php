<?php
include 'db.php';

$message = "";

// Handle Add Student using Stored Procedure
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $dob = $conn->real_escape_string($_POST['dob']);
    $age = intval($_POST['age']);
    $address = $conn->real_escape_string($_POST['address']);

    // Calling Stored Procedure
    $sql = "CALL sp_add_student('$first_name', '$last_name', '$email', '$dob', $age, '$address')";
    
    try {
        if ($conn->query($sql) === TRUE) {
            $message = "<div class='alert alert-success'>Student added successfully via Stored Procedure!</div>";
        } else {
             $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
        }
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}

// Handle Delete Student
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM students WHERE id=$id");
    header("Location: students.php"); 
    exit;
}

include 'header.php';
?>

<div class="page-header">
    <h2 class="page-title">Students</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
        <i class="fas fa-plus me-2"></i>Add Student
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
                    <th>Age</th>
                    <th>Address</th>
                    <th>DOB</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $result = $conn->query("SELECT *, CONCAT(first_name, ' ', last_name) as full_name FROM students ORDER BY id DESC");
                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>#" . $row['id'] . "</td>";
                            echo "<td class='fw-bold'>" . htmlspecialchars($row['full_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                            echo "<td>" . (isset($row['age']) ? $row['age'] : '-') . "</td>";
                            echo "<td><small class='text-muted'>" . (isset($row['address']) ? htmlspecialchars($row['address']) : '-') . "</small></td>";
                            echo "<td>" . $row['dob'] . "</td>";
                            echo "<td class='text-end'>
                                    <a href='edit_student.php?id=" . $row['id'] . "' class='btn btn-light btn-sm text-primary me-1'><i class='fas fa-edit'></i></a>
                                    <a href='students.php?delete=" . $row['id'] . "' class='btn btn-light btn-sm text-danger' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i></a>
                                </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center py-4'>No students found</td></tr>";
                    }
                } catch (Exception $e) {
                     echo "<tr><td colspan='7' class='text-center text-danger py-4'>Database Error: " . $e->getMessage() . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Add New Student (SP)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="students.php">
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
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" name="dob" required>
                        </div>
                         <div class="col-6 mb-3">
                            <label class="form-label">Age</label>
                            <input type="number" class="form-control" name="age" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="2" required></textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Save Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
