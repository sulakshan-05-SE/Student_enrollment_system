<?php
include 'db.php';
include 'header.php';

// Fetch stats
$student_count_res = $conn->query("SELECT count(*) as count FROM students");
$student_count = $student_count_res ? $student_count_res->fetch_assoc()['count'] : 0;

$teacher_count_res = $conn->query("SELECT count(*) as count FROM teachers");
$teacher_count = $teacher_count_res ? $teacher_count_res->fetch_assoc()['count'] : 0;

$course_count_res = $conn->query("SELECT count(*) as count FROM courses");
$course_count = $course_count_res ? $course_count_res->fetch_assoc()['count'] : 0;

$enrollment_count_res = $conn->query("SELECT count(*) as count FROM enrollments");
$enrollment_count = $enrollment_count_res ? $enrollment_count_res->fetch_assoc()['count'] : 0;
?>

<div class="row mb-5">
    <div class="col-md-12 text-center">
        <h1 class="display-4 fw-bold mb-3">Welcome to Trinco ati</h1>
        <p class="lead text-secondary">Manage your students, teachers, and courses with ease.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Students Card -->
    <div class="col-md-3">
        <div class="card h-100 p-4 text-center">
            <div class="card-body">
                <i class="fas fa-user-graduate stats-icon text-primary"></i>
                <h3 class="card-title fw-bold"><?php echo $student_count; ?></h3>
                <p class="text-secondary">Total Students</p>
                <a href="students.php" class="btn btn-outline-primary btn-sm mt-2">Manage</a>
            </div>
        </div>
    </div>

    <!-- Teachers Card -->
    <div class="col-md-3">
        <div class="card h-100 p-4 text-center">
            <div class="card-body">
                <i class="fas fa-chalkboard-teacher stats-icon text-success"></i>
                <h3 class="card-title fw-bold"><?php echo $teacher_count; ?></h3>
                <p class="text-secondary">Total Teachers</p>
                <a href="teachers.php" class="btn btn-outline-success btn-sm mt-2">Manage</a>
            </div>
        </div>
    </div>

    <!-- Courses Card -->
    <div class="col-md-3">
        <div class="card h-100 p-4 text-center">
            <div class="card-body">
                <i class="fas fa-book-open stats-icon text-warning"></i>
                <h3 class="card-title fw-bold"><?php echo $course_count; ?></h3>
                <p class="text-secondary">Active Courses</p>
                <a href="courses.php" class="btn btn-outline-warning btn-sm mt-2">Manage</a>
            </div>
        </div>
    </div>
    
    <!-- Enrollments Card -->
    <div class="col-md-3">
        <div class="card h-100 p-4 text-center">
            <div class="card-body">
                <i class="fas fa-database stats-icon text-danger"></i>
                <h3 class="card-title fw-bold"><?php echo $enrollment_count; ?></h3>
                <p class="text-secondary">Total Enrollments</p>
                <a href="enrollments.php" class="btn btn-outline-danger btn-sm mt-2">Manage</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-6">
        <div class="card p-4">
            <h4 class="mb-4">Quick Actions</h4>
            <div class="d-grid gap-2">
                <a href="enrollments.php" class="btn btn-primary"><i class="fas fa-plus-circle me-2"></i>Enroll New Student</a>
                <a href="students.php" class="btn btn-light text-start"><i class="fas fa-user-plus me-2"></i>Add Student</a>
                <a href="courses.php" class="btn btn-light text-start"><i class="fas fa-book me-2"></i>Create Course</a>
            </div>
        </div>
    </div>
    
</div>

<?php include 'footer.php'; ?>
