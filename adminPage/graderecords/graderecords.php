<?php
session_start();

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Check if user is logged in
if (!isset($_SESSION['role'])) {
    header("Location: ../../Login_FacultyPage/loginFaculty.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Records - ISCP Admin</title>
    <link rel="stylesheet" href="../sidebar.css">
    <link rel="stylesheet" href="graderecords.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

    <?php include '../sidebar.php'; ?>

    <main class="main-content">
        <header class="top-header">
            <div class="header-left">
                <h1>Academic Performance</h1>
                <p>Strict filtering by course to ensure accurate grade management.</p>
            </div>
        </header>

        <div class="course-tabs" id="courseFilter">
            <button class="tab-btn active" data-filter="BSCS">BSCS</button>
            <button class="tab-btn" data-filter="BSIT">BSIT</button>
            <button class="tab-btn" data-filter="BS CRIM">BS CRIM</button>
            <button class="tab-btn" data-filter="BS ARCHI">BS ARCHI</button>
            <button class="tab-btn" data-filter="BSED">BSED</button>
        </div>

        <section class="table-container">
            <div class="glass-card">
                <div class="card-header flex-header">
                    <h3>Student Masterlist: <span id="activeCourseTitle" class="highlight">BSCS</span></h3>
                    <div class="search-box">
                        <input type="text" id="studentSearch" placeholder="Search name in this course...">
                    </div>
                </div>

                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Full Name</th>
                                <th>Year Level</th>
                                <th>General Average</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="studentTableBody">
                            <tr class="grade-row-item" data-course="BSCS">
                                <td><strong>2023-0102</strong></td>
                                <td class="student-name">Dela Cruz, Juan A.</td>
                                <td>1st Year</td>
                                <td><span class="average-val">1.25</span></td>
                                <td><button class="btn-update-trigger" onclick="openUpdateModal('Dela Cruz, Juan A.', '2023-0102')">Update</button></td>
                            </tr>
                            <tr class="grade-row-item" data-course="BSIT" style="display: none;">
                                <td><strong>2023-0542</strong></td>
                                <td class="student-name">Razo, Justine James</td>
                                <td>2nd Year</td>
                                <td><span class="average-val">1.12</span></td>
                                <td><button class="btn-update-trigger" onclick="openUpdateModal('Razo, Justine James', '2023-0542')">Update</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <div id="gradeModal" class="modal-overlay">
        <div class="modal-container">
            <span class="close-modal" id="closeModalBtn">&times;</span>
            <div class="modal-header">
                <h2>Detailed Grade Report</h2>
                <p>Student: <strong id="modalStudentName">--</strong> | <span id="modalStudentID">--</span></p>
            </div>
            <div class="modal-body">
                <table class="modal-grade-table">
                    <thead>
                        <tr><th>Subject</th><th>Prelim</th><th>Midterm</th><th>Finals</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <tr class="modal-row">
                            <td class="subj-name">Intro to Computing</td>
                            <td><input type="text" class="modal-input" value="1.00"></td>
                            <td><input type="text" class="modal-input" value="1.25"></td>
                            <td><input type="text" class="modal-input" value="1.25"></td>
                            <td><span class="status-badge status-p">P</span></td>
                        </tr>
                    </tbody>
                </table>
                <button class="btn-save">Confirm Changes</button>
            </div>
        </div>
    </div>

    <script src="../sidebar.js"></script>
    <script src="graderecords.js"></script>
</body>
</html>