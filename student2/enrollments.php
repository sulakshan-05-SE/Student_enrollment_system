<?php
include 'db.php';

$message = "";

// Handle Enroll using Stored Procedure
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'enroll') {
    $student_id = intval($_POST['student_id']);

    // Check if course_id is an array (multiple selection) or single value
    $course_ids = isset($_POST['course_id']) ? $_POST['course_id'] : [];
    if (!is_array($course_ids)) {
        $course_ids = [$course_ids];
    }

    $success_count = 0;
    $error_count = 0;
    $errors = [];

    foreach ($course_ids as $cid) {
        $course_id = intval($cid);
        if ($course_id <= 0) continue;

        // Check if already enrolled
        $check = $conn->query("SELECT * FROM enrollments WHERE student_id=$student_id AND course_id=$course_id");
        if ($check->num_rows > 0) {
            $error_count++; // Already enrolled
        } else {
            // Use Stored Procedure
            $sql = "CALL sp_enroll_student($student_id, $course_id)";
            try {
                if ($conn->query($sql) === TRUE) {
                    $success_count++;
                } else {
                    $error_count++;
                    $errors[] = $conn->error;
                }
            } catch (Exception $e) {
                $error_count++;
                $errors[] = $e->getMessage();
            }
        }
    }

    if ($success_count > 0 && $error_count == 0) {
        $message = "<div class='alert alert-success'>Student enrolled in $success_count course(s) successfully!</div>";
    } elseif ($success_count > 0 && $error_count > 0) {
        $message = "<div class='alert alert-warning'>Enrolled in $success_count course(s), but failed/already enrolled in $error_count course(s).</div>";
    } else {
        $message = "<div class='alert alert-warning'>No new enrollments made. Check if student is already enrolled.</div>";
    }
}

// Handle Unenroll
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM enrollments WHERE id=$id");
    header("Location: enrollments.php");
    exit;
}

// Fetch lists for dropdowns
$students_res = $conn->query("SELECT id, first_name, last_name FROM students");
$courses_res = $conn->query("SELECT id, name FROM courses");

include 'header.php';
?>

<div class="page-header">
    <h2 class="page-title">Enrollments & Reports</h2>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-outline-secondary">
            <i class="fas fa-print me-2"></i>Print Report
        </button>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#enrollModal">
            <i class="fas fa-plus me-2"></i>Enroll Student
        </button>
    </div>
</div>

<?php echo $message; ?>

<div class="card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table-custom mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Course Name</th>
                    <th>Teacher</th>
                    <th>Enrollment Date</th>
                    <th class="text-end d-print-none">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php

                $result = $conn->query("CALL sp_get_enrollment_report()");


                if (!$result) {

                    $sql = "SELECT e.id, CONCAT(s.first_name, ' ', s.last_name) as student_name, 
                            c.name as course_name, 
                            CONCAT(t.first_name, ' ', t.last_name) as teacher_name, 
                            e.enrollment_date 
                            FROM enrollments e
                            INNER JOIN students s ON e.student_id = s.id
                            INNER JOIN courses c ON e.course_id = c.id
                            INNER JOIN teachers t ON c.teacher_id = t.id
                            ORDER BY e.enrollment_date DESC";
                    $result = $conn->query($sql);
                }

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>#" . $row['id'] . "</td>";
                        echo "<td class='fw-bold'>" . htmlspecialchars($row['student_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['course_name']) . "</td>";
                        // Since it's INNER JOIN, teacher_name is guaranteed to exist
                        echo "<td><span class='badge bg-light text-dark border'>" . htmlspecialchars($row['teacher_name']) . "</span></td>";
                        echo "<td>" . date('M d, Y', strtotime($row['enrollment_date'])) . "</td>";
                        echo "<td class='text-end d-print-none'>
                                <a href='enrollments.php?delete=" . $row['id'] . "' class='btn btn-light btn-sm text-danger' onclick='return confirm(\"Unenroll this student?\")'><i class='fas fa-trash'></i></a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center py-4'>No enrollments found (Note: Courses without teachers are hidden due to Inner Join)</td></tr>";
                }

                // Free result to allow further queries if needed
                if ($result) {
                    $result->free();
                    $conn->next_result();
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Enroll Modal -->
<div class="modal fade" id="enrollModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Enroll Student in Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="enrollments.php">
                    <input type="hidden" name="action" value="enroll">
                    <div class="mb-3">
                        <label class="form-label">Student</label>
                        <select class="form-select" name="student_id" required>
                            <option value="">Select a student...</option>
                            <?php
                            if ($students_res && $students_res->num_rows > 0) {
                                while ($s = $students_res->fetch_assoc()) {
                                    echo "<option value='" . $s['id'] . "'>" . htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Courses (Hold Ctrl/Cmd to select multiple)</label>
                        <select class="form-select" name="course_id[]" multiple required style="height: 150px;">
                            <?php
                            if ($courses_res && $courses_res->num_rows > 0) {
                                while ($c = $courses_res->fetch_assoc()) {
                                    echo "<option value='" . $c['id'] . "'>" . htmlspecialchars($c['name']) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Enroll</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>