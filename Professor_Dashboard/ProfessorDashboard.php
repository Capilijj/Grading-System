<?php
/**
 * ProfessorDashboard.php
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Database Connection
$connectionFile = __DIR__ . '/../Database/database_Connection.php';
if (file_exists($connectionFile)) {
    include $connectionFile;
} else {
    die("Database connection file missing.");
}

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// 2. Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Professor') {
    header("Location: ../Login_FacultyPage/loginFaculty.php");
    exit();
}

// User Info mula sa Session
$professorID = $_SESSION['professorID'] ?? $_SESSION['user_id']; 
$professor_full_name = $_SESSION['fullName'] ?? 'Professor';

/* --- CALL SP: GET METRICS --- */
try {
    $stmt_metrics = $conn->prepare("{call sp_GetProfessorMetrics(?)}");
    $stmt_metrics->execute([$professorID]);
    $metrics = $stmt_metrics->fetch(PDO::FETCH_ASSOC) ?: ['SubjectsHandled' => 0, 'TotalStudents' => 0];
    $stmt_metrics->closeCursor();
} catch (Exception $e) {
    $metrics = ['SubjectsHandled' => 0, 'TotalStudents' => 0];
}

/* --- CALL SP: GET ALL SCHEDULES --- */
$schedules = [];
try {
    $stmt_sched = $conn->prepare("{call sp_GetProfessorSchedule(?)}");
    $stmt_sched->execute([$professorID]);
    $schedules = $stmt_sched->fetchAll(PDO::FETCH_ASSOC);
    $stmt_sched->closeCursor();
} catch (Exception $e) {
    $schedules = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professor Dashboard - ISCP</title>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <link rel="stylesheet" href="/Professor_Dashboard/Header/ProfessorHeader.css">
    <link rel="stylesheet" href="ProfessorDashboard.css">
    <link rel="stylesheet" href="../User_Dashboard/Footer/FooterDashboard.css">
    
    <style>
        /* CSS FIX: Siguraduhin na ang dropdown menu ay laging nasa itaas */
        header { position: relative; z-index: 1000 !important; }
        .faculty-main { position: relative; z-index: 1; }
        
        /* Dashboard Enhancements */
        .m-number { font-weight: bold; }
        .day-label { font-weight: 700; color: #0c225e; }
        .time-highlight { color: #d35400; font-weight: 600; }
        
        /* Grading Scale Note */
        .grading-note {
            font-size: 0.8rem;
            color: #666;
            margin-top: 5px;
            display: block;
        }
    </style>
</head>
<body>

    <?php include 'Header/ProfessorHeader.php'; ?>

    <main class="faculty-main">
        <section class="faculty-hero">
            <div class="hero-text">
                <h1>HI, <?php echo htmlspecialchars($professor_full_name); ?>!</h1>
                <p>International State College of the Philippines | Faculty Portal</p>
                <small>Professor ID: <?php echo htmlspecialchars($professorID); ?></small>
            </div>
        </section>

        <section class="metrics-grid">
            <div class="m-card">
                <div class="m-icon" style="background: #e67e22;">📚</div>
                <div class="m-data">
                    <span class="m-label">Total Subjects</span>
                    <span class="m-number"><?php echo str_pad($metrics['SubjectsHandled'], 2, "0", STR_PAD_LEFT); ?></span>
                </div>
            </div>
            <div class="m-card">
                <div class="m-icon" style="background: #16a085;">👨‍🎓</div>
                <div class="m-data">
                    <span class="m-label">Total Students</span>
                    <span class="m-number"><?php echo number_format($metrics['TotalStudents']); ?></span>
                </div>
            </div>
            <div class="m-card">
                <div class="m-icon" style="background: #2980b9;">🕒</div>
                <div class="m-data">
                    <span class="m-label">Schedule Records</span>
                    <span class="m-number"><?php echo str_pad(count($schedules), 2, "0", STR_PAD_LEFT); ?></span>
                </div>
            </div>
        </section>

        <div class="lower-grid full-width" id="schedule-content">
            <div class="card-container table-card">
                <div class="card-title">
                    <div>
                        <h3>Weekly Class Schedule</h3>
                        <span class="grading-note">Note: Grading system follows the 1.0 - 5.0 College Scale.</span>
                    </div>
                    <div class="card-actions">
                        <button class="btn-download" id="downloadSchedBtn">
                            <span>📥</span> Download PDF
                        </button>
                    </div>
                </div>
                
                <div class="table-wrapper">
                    <table class="fac-table">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Subject Code & Description</th>
                                <th>Section</th>
                                <th>Time</th>
                                <th>Room</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($schedules)): ?>
                                <tr>
                                    <td colspan="5" style="text-align:center; padding: 30px;">
                                        No schedule records found for this semester.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($schedules as $sched): ?>
                                <tr>
                                    <td><span class="day-label"><?php echo htmlspecialchars($sched['dayOfWeek']); ?></span></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($sched['subjectCode']); ?></strong> 
                                        - <?php echo htmlspecialchars($sched['subjectName']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($sched['sectionName']); ?></td>
                                    <td class="time-highlight">
                                        <?php echo htmlspecialchars($sched['StartTime']) . ' - ' . htmlspecialchars($sched['EndTime']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($sched['room'] ?? 'TBA'); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php include '../User_Dashboard/Footer/FooterDashboard.php'; ?>

    <script src="/Professor_Dashboard/Header/ProfessorHeader.js"></script>
    <script src="ProfessorDashboard.js"></script>
</body>
</html>