<?php
session_start();
// Database connection path - Original Path
require_once __DIR__ . '/../../Database/database_Connection.php';

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Check if student is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Student') {
    header("Location: ../../Login_StudentPage/loginStudent.php");
    exit();
}

$studentID = $_SESSION['studentID'] ?? '';

try {
    $sql = "{call sp_GetStudentGrades(?)}";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$studentID]);
    $all_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $studentInfo = $all_data[0] ?? null;
} catch (Exception $e) {
    $errorMsg = $e->getMessage();
}

/**
 * AUTOMATED ACADEMIC YEAR & SEMESTER LOGIC
 */
$currentMonth = (int)date('n');
$currentYear = (int)date('Y');
if ($currentMonth >= 6 && $currentMonth <= 11) {
    $semester = "First Semester";
    $academicYear = $currentYear . "-" . ($currentYear + 1);
} else {
    $semester = "Second Semester";
    $academicYear = ($currentMonth >= 1 && $currentMonth <= 5) ? ($currentYear - 1) . "-" . $currentYear : $currentYear . "-" . ($currentYear + 1);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grades - ISCP</title>
    
    <link rel="stylesheet" href="../Header/header.css">
    <link rel="stylesheet" href="grade.css">
    <link rel="stylesheet" href="../Footer/FooterDashboard.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <style>
        /* CSS Fix para sa dropdown visibility */
        header { position: relative; z-index: 1000 !important; }
        .grade-container { position: relative; z-index: 1; }
        .gpa-value { font-weight: bold; color: #d35400; }
        .badge-status { padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; }
        
        /* Status Colors */
        .passed { color: #27ae60; font-weight: bold; }
        .failed { color: #e74c3c; font-weight: bold; }
        .pending { color: #f39c12; font-weight: bold; }
    </style>
</head>
<body>

    <?php include '../Header/header.php'; ?>

    <div id="grade-content">
        <main class="grade-container">
            
            <div class="student-info-card">
                <div class="student-name-id">
                    <?php echo strtoupper($studentInfo['fullName'] ?? 'STUDENT NAME'); ?> (<?php echo htmlspecialchars($studentID); ?>)
                </div>
                
                <div class="academic-details">
                    <div class="detail-row">
                        <span class="badge">School Year <?php echo $academicYear; ?> <?php echo $semester; ?></span>
                    </div>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Admission Status</label>
                            <span>Continuing</span>
                        </div>
                        <div class="info-item">
                            <label>Scholastic Status</label>
                            <span>Regular</span>
                        </div>
                        <div class="info-item">
                            <label>Course Code & Description</label>
                            <span><?php echo htmlspecialchars($studentInfo['courseDescription'] ?? 'N/A'); ?></span>
                        </div>
                    </div>

                    <div class="gpa-section">
                        <label>GPA (1.00 is Highest / Displayed if all grades are in)</label>
                        <span class="gpa-value">
                            <?php 
                                // Ito ay magdi-display ng 1.00 - 5.00 o "Grades Not Complete"
                                echo $studentInfo['currentGPA'] ?? 'Grades Not Complete'; 
                            ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="white-content-container">
                <div class="grade-header">
                    <span>Final Grades (Uno Scale)</span>
                    <button onclick="downloadGradePDF()" class="btn-download">Download Grade 📥</button>
                </div>
                
                <div class="grade-table-wrapper">
                    <table class="grade-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Subject Code</th>
                                <th>Description</th>
                                <th>Faculty Name</th>
                                <th>Unit</th>
                                <th>Final Grade</th>
                                <th>Grade Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $counter = 0;
                            if (!empty($all_data) && isset($all_data[0]['subjectCode'])):
                                foreach ($all_data as $row): 
                                    $counter++;
                                    $statusClass = strtolower($row['gradeStatus']);
                            ?>
                                <tr>
                                    <td><?php echo $counter; ?></td>
                                    <td><?php echo htmlspecialchars($row['subjectCode']); ?></td>
                                    <td><?php echo htmlspecialchars($row['subjectDesc']); ?></td>
                                    <td><?php echo htmlspecialchars($row['facultyName']); ?></td>
                                    <td><?php echo number_format($row['units'] ?? 3.0, 1); ?></td>
                                    <td style="font-weight: bold;"><?php echo $row['finalGrade'] ?? '---'; ?></td>
                                    <td>
                                        <span class="badge-status <?php echo $statusClass; ?>">
                                            <?php echo $row['gradeStatus']; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php 
                                endforeach; 
                            else:
                                echo "<tr><td colspan='7' style='text-align:center; padding: 20px;'>No subjects assigned.</td></tr>";
                            endif; 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <?php include '../Footer/FooterDashboard.php'; ?>

    <script src="../Header/header.js"></script>
    <script src="grade.js"></script>
</body>
</html>