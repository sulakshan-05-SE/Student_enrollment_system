<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Enrollment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-bg: #f3f4f6;
            --sidebar-bg: #ffffff;
            --card-bg: #ffffff;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --accent-color: #4f46e5;
            --accent-hover: #4338ca;
            --glass-bg: rgba(255, 255, 255, 0.95);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--primary-bg);
            color: var(--text-primary);
            overflow-x: hidden;
        }

        .navbar {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--accent-color);
            font-size: 1.5rem;
        }

        .nav-link {
            font-weight: 500;
            color: var(--text-secondary);
            margin: 0 0.5rem;
            transition: color 0.3s ease;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--accent-color);
        }

        .btn-primary {
            background-color: var(--accent-color);
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--accent-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 8px -1px rgba(79, 70, 229, 0.3);
        }

        .card {
            background: var(--card-bg);
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }

        .page-header {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
        }
        
        .table-custom {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 0.5rem;
        }

        .table-custom th {
            font-weight: 600;
            color: var(--text-secondary);
            padding: 1rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .table-custom td {
            background: white;
            padding: 1rem;
            border-top: 1px solid #f3f4f6;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        .table-custom tr td:first-child {
            border-top-left-radius: 0.5rem;
            border-bottom-left-radius: 0.5rem;
        }
        
        .table-custom tr td:last-child {
            border-top-right-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
        }

        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--accent-color);
            opacity: 0.8;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fas fa-graduation-cap me-2"></i>Trinco Ati</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="students.php">Students</a></li>
                <li class="nav-item"><a class="nav-link" href="teachers.php">Teachers</a></li>
                <li class="nav-item"><a class="nav-link" href="courses.php">Courses</a></li>
                <li class="nav-item"><a class="nav-link" href="enrollments.php">Enrollments</a></li>
                <li class="nav-item"><a class="nav-link" href="reports.php">Reports</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-5">
