<?php
session_start();
require_once __DIR__ . '/../../Database/database_Connection.php';

// No-cache headers
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Student access check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Student') {
    header("Location: ../../Login_StudentPage/loginStudent.php");
    exit();
}

$studentID = $_SESSION['studentID'] ?? '';
$studentInfo = null;
$all_data = [];

try {
    // Fetch student grades using updated SP
    $sql = "{call sp_GetStudentGrades(?)}";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$studentID]);
    $all_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $studentInfo = $all_data[0] ?? null;
} catch (Exception $e) {
    $errorMsg = $e->getMessage();
}

// SY & Semester Logic
$currentMonth = (int)date('n');
$currentYear = (int)date('Y');
$semester = ($currentMonth >= 6 && $currentMonth <= 11) ? "First Semester" : "Second Semester";
$academicYear = ($semester == "First Semester") ? $currentYear . "-" . ($currentYear + 1) : ($currentYear - 1) . "-" . $currentYear;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Grades - ISCP</title>
    <link rel="stylesheet" href="../Header/header.css">
    <link rel="stylesheet" href="grade.css">
    <link rel="stylesheet" href="../Footer/FooterDashboard.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>
<body>
    <?php include '../Header/header.php'; ?>

    <div id="grade-content">
        <main class="grade-container">
            <div class="student-info-card">
                <div class="student-name-id">
                    <?php echo strtoupper($studentInfo['fullName'] ?? 'STUDENT'); ?> (<?php echo htmlspecialchars($studentID); ?>)
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
                        <label>GPA (1.00 is Highest)</label>
                        <?php 
                        $displayGPA = isset($studentInfo['currentGPA']) && $studentInfo['currentGPA'] > 0 
                            ? number_format($studentInfo['currentGPA'], 2) 
                            : '---';
                        ?>
                        <span class="gpa-value"><?php echo $displayGPA; ?></span>
                        <?php if ($displayGPA === '---'): ?>
                            <small style="color: #999; font-size: 0.75rem; margin-left: 10px;">
                                (Available after all subjects are graded)
                            </small>
                        <?php endif; ?>
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
                                    // Match status to CSS classes
                                    $statusClass = strtolower($row['gradeStatus'] ?? 'pending');
                                    
                                    // Handle different status values
                                    if (stripos($statusClass, 'pass') !== false) $statusClass = 'passed';
                                    elseif (stripos($statusClass, 'inc') !== false) $statusClass = 'incomplete';
                                    elseif (stripos($statusClass, 'fail') !== false) $statusClass = 'failed';
                                    else $statusClass = 'pending';
                            ?>
                                <tr>
                                    <td><?php echo $counter; ?></td>
                                    <td><?php echo htmlspecialchars($row['subjectCode']); ?></td>
                                    <td><?php echo htmlspecialchars($row['subjectDesc']); ?></td>
                                    <td><?php echo htmlspecialchars($row['facultyName'] ?? 'TBA'); ?></td>
                                    <td><?php echo number_format($row['units'] ?? 3.0, 1); ?></td>
                                    <td style="font-weight: bold; color: #043b68;">
                                        <?php echo !empty($row['finalGrade']) ? $row['finalGrade'] : '---'; ?>
                                    </td>
                                    <td>
                                        <span class="badge-status <?php echo $statusClass; ?>">
                                            <?php echo strtoupper($row['gradeStatus'] ?? 'PENDING'); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php 
                                endforeach; 
                            else:
                                echo "<tr><td colspan='7' style='text-align:center; padding: 20px; color: #999;'>No grade records found for this academic year.</td></tr>";
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