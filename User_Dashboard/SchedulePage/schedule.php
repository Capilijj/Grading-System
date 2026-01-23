<?php
session_start();

// 1. Database Connection
require_once '../../Database/database_Connection.php'; 

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Check if student is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Student') {
    header("Location: ../../Login_StudentPage/loginStudent.php");
    exit();
}

// Kunin ang Student ID mula sa Session
$studentID = $_SESSION['studentID'] ?? $_SESSION['user_id'] ?? '';

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

/* ---------------------------------------------------------
    FETCH SCHEDULE FROM DATABASE USING SP
   --------------------------------------------------------- */
$schedules = [];
try {
    // Tinatawag ang SP na inayos natin sa SQL Server
    $stmt = $conn->prepare("EXEC sp_GetStudentSchedule ?");
    $stmt->execute([$studentID]);
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
} catch (Exception $e) {
    $error_msg = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Schedule - ISCP</title>
    
    <link rel="stylesheet" href="../Header/header.css">
    <link rel="stylesheet" href="schedule.css">
    <link rel="stylesheet" href="../Footer/FooterDashboard.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>
<body>

    <?php include '../Header/header.php'; ?>

    <div id="schedule-content">
        <main class="schedule-container">
            <div class="white-content-container">
                
                <div class="page-title-inside">
                    <h2>MY CLASS SCHEDULE</h2>
                    <p>Academic Year <?php echo $academicYear; ?> | <?php echo $semester; ?></p>
                </div>

                <div class="schedule-header">
                    <span>Weekly Class Schedule</span>
                    <button onclick="downloadPDF()" class="print-btn btn-download">
                        Download Schedule 📥
                    </button>
                </div>
                
                <div class="schedule-table-wrapper">
                    <table class="schedule-table">
                        <thead>
                            <tr>
                                <th>Subject Code</th>
                                <th>Description</th>
                                <th>Units</th>
                                <th>Day</th>
                                <th>Time</th>
                                <th>Room</th>
                                <th>Instructor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($schedules)): ?>
                                <?php foreach ($schedules as $row): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($row['subjectCode']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($row['subjectName']); ?></td>
                                        <td><?php echo htmlspecialchars($row['units']); ?></td>
                                        <td><?php echo htmlspecialchars($row['dayOfWeek']); ?></td>
                                        <td><?php echo $row['StartTime'] . ' - ' . $row['EndTime']; ?></td>
                                        <td><?php echo htmlspecialchars($row['room']); ?></td>
                                        <td><?php echo htmlspecialchars($row['instructorName']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align:center; padding: 20px; color: #888;">
                                        No schedule found for Student ID: <?php echo htmlspecialchars($studentID); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <?php include '../Footer/FooterDashboard.php'; ?>

    <script src="../Header/header.js"></script>
    <script src="schedule.js"></script>
</body>
</html>