<?php
include 'db.php';
include 'header.php';

$report_type = isset($_GET['type']) ? $_GET['type'] : 'all';
$filter_id = isset($_GET['filter_id']) ? intval($_GET['filter_id']) : 0;
$results = [];

// Fetch dropdown data
$courses_list = $conn->query("SELECT id, name FROM courses");
$students_list = $conn->query("SELECT id, CONCAT(first_name, ' ', last_name) as name FROM students");
$teachers_list = $conn->query("SELECT id, CONCAT(first_name, ' ', last_name) as name FROM teachers");

// Report Logic
if ($report_type == 'course' && $filter_id > 0) {
    // Get Course Details
    $course_info = $conn->query("SELECT * FROM courses WHERE id=$filter_id")->fetch_assoc();
    $report_title = "Class List: " . $course_info['name'];
    
    // Get Enrolled Students
    $sql = "SELECT s.id, CONCAT(s.first_name, ' ', s.last_name) as student_name, s.email, e.enrollment_date 
            FROM enrollments e 
            JOIN students s ON e.student_id = s.id 
            WHERE e.course_id = $filter_id";
    $results = $conn->query($sql);
    
} elseif ($report_type == 'student' && $filter_id > 0) {
    // Get Student Details
    $student_info = $conn->query("SELECT CONCAT(first_name, ' ', last_name) as name FROM students WHERE id=$filter_id")->fetch_assoc();
    $report_title = "Student Transcript: " . $student_info['name'];
    
    // Get Enrolled Courses
    $sql = "SELECT c.id, c.name as course_name, c.fees, c.duration, CONCAT(t.first_name, ' ', t.last_name) as teacher_name, e.enrollment_date 
            FROM enrollments e 
            JOIN courses c ON e.course_id = c.id 
            LEFT JOIN teachers t ON c.teacher_id = t.id 
            WHERE e.student_id = $filter_id";
    $results = $conn->query($sql);

} elseif ($report_type == 'teacher' && $filter_id > 0) {
    // Get Teacher Details
    $teacher_info = $conn->query("SELECT CONCAT(first_name, ' ', last_name) as name FROM teachers WHERE id=$filter_id")->fetch_assoc();
    $report_title = "Teacher Course Load: " . $teacher_info['name'];

    // Get Courses and student count
    $sql = "SELECT c.id, c.name as course_name, c.duration, count(e.id) as student_count 
            FROM courses c 
            LEFT JOIN enrollments e ON c.id = e.course_id 
            WHERE c.teacher_id = $filter_id 
            GROUP BY c.id";
    $results = $conn->query($sql);
} else {
    $report_title = "General Enrollment Report";
    $sql = "SELECT e.id, CONCAT(s.first_name, ' ', s.last_name) as student_name, c.name as course_name, CONCAT(t.first_name, ' ', t.last_name) as teacher_name, e.enrollment_date 
            FROM enrollments e
            JOIN students s ON e.student_id = s.id
            JOIN courses c ON e.course_id = c.id
            LEFT JOIN teachers t ON c.teacher_id = t.id
            ORDER BY e.enrollment_date DESC";
    $results = $conn->query($sql);
}
?>

<div class="d-print-none">
    <div class="page-header">
        <h2 class="page-title">System Reports</h2>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="reports.php" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Report Type</label>
                    <select class="form-select" name="type" id="reportType" onchange="toggleFilters()">
                        <option value="all" <?php echo $report_type == 'all' ? 'selected' : ''; ?>>All Enrollments</option>
                        <option value="course" <?php echo $report_type == 'course' ? 'selected' : ''; ?>>By Course (Class List)</option>
                        <option value="student" <?php echo $report_type == 'student' ? 'selected' : ''; ?>>By Student (Transcript)</option>
                        <option value="teacher" <?php echo $report_type == 'teacher' ? 'selected' : ''; ?>>By Teacher (Workload)</option>
                    </select>
                </div>

                <div class="col-md-4" id="courseFilter" style="display:none;">
                    <label class="form-label">Select Course</label>
                    <select class="form-select" name="filter_id" id="courseSelect">
                        <?php while($row = $courses_list->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>" <?php echo ($report_type == 'course' && $filter_id == $row['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-4" id="studentFilter" style="display:none;">
                    <label class="form-label">Select Student</label>
                    <select class="form-select" name="filter_id" id="studentSelect" disabled>
                        <?php while($row = $students_list->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>" <?php echo ($report_type == 'student' && $filter_id == $row['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="col-md-4" id="teacherFilter" style="display:none;">
                    <label class="form-label">Select Teacher</label>
                    <select class="form-select" name="filter_id" id="teacherSelect" disabled>
                        <?php while($row = $teachers_list->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>" <?php echo ($report_type == 'teacher' && $filter_id == $row['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-2"></i>Generate</button>
                </div>
                <div class="col-md-2">
                     <button type="button" onclick="window.print()" class="btn btn-outline-secondary w-100"><i class="fas fa-print me-2"></i>Print</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card p-4">
    <div class="text-center mb-4">
        <h3 class="fw-bold"><?php echo $report_title; ?></h3>
        <p class="text-muted">Generated on <?php echo date('M d, Y H:i'); ?></p>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <?php if ($report_type == 'course'): ?>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Enrollment Date</th>
                    <?php elseif ($report_type == 'student'): ?>
                        <th>Course</th>
                        <th>Teacher</th>
                        <th>Fees</th>
                        <th>Duration</th>
                        <th>Enrolled On</th>
                    <?php elseif ($report_type == 'teacher'): ?>
                        <th>Course Name</th>
                        <th>Duration</th>
                        <th>Students Enrolled</th>
                    <?php else: ?>
                        <th>Date</th>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Teacher</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if ($results && $results->num_rows > 0): ?>
                    <?php while($row = $results->fetch_assoc()): ?>
                        <tr>
                            <?php if ($report_type == 'course'): ?>
                                <td>#<?php echo $row['id']; ?></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($row['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($row['enrollment_date'])); ?></td>
                            <?php elseif ($report_type == 'student'): ?>
                                <td class="fw-bold"><?php echo htmlspecialchars($row['course_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['teacher_name']); ?></td>
                                <td>LKR <?php echo number_format($row['fees'], 2); ?></td>
                                <td><?php echo htmlspecialchars($row['duration']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($row['enrollment_date'])); ?></td>
                            <?php elseif ($report_type == 'teacher'): ?>
                                <td class="fw-bold"><?php echo htmlspecialchars($row['course_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['duration']); ?></td>
                                <td><span class="badge bg-primary rounded-pill"><?php echo $row['student_count']; ?></span></td>
                            <?php else: ?>
                                <td><?php echo date('M d, Y', strtotime($row['enrollment_date'])); ?></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($row['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['teacher_name']); ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center py-4">No records found for the selected criteria.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleFilters() {
    const type = document.getElementById('reportType').value;
    const courseFilter = document.getElementById('courseFilter');
    const studentFilter = document.getElementById('studentFilter');
    const teacherFilter = document.getElementById('teacherFilter');
    
    // Reset all logic
    courseFilter.style.display = 'none';
    studentFilter.style.display = 'none';
    teacherFilter.style.display = 'none';
    
    document.getElementById('courseSelect').disabled = true;
    document.getElementById('studentSelect').disabled = true;
    document.getElementById('teacherSelect').disabled = true;

    if (type === 'course') {
        courseFilter.style.display = 'block';
        document.getElementById('courseSelect').disabled = false;
    } else if (type === 'student') {
        studentFilter.style.display = 'block';
        document.getElementById('studentSelect').disabled = false;
    } else if (type === 'teacher') {
        teacherFilter.style.display = 'block';
        document.getElementById('teacherSelect').disabled = false;
    }
}

// Init on load
document.addEventListener('DOMContentLoaded', toggleFilters);
</script>

<?php include 'footer.php'; ?>
