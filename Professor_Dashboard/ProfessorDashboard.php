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

/* --- CALL SP: GET METRICS (Updated with CoursesList) --- */
try {
    $stmt_metrics = $conn->prepare("{call sp_GetProfessorMetrics(?)}");
    $stmt_metrics->execute([$professorID]);
    $metrics = $stmt_metrics->fetch(PDO::FETCH_ASSOC) ?: [
        'SubjectsHandled' => 0, 
        'TotalStudents' => 0,
        'CoursesList' => 'No subjects assigned'
    ];
    $stmt_metrics->closeCursor();
} catch (Exception $e) {
    $metrics = [
        'SubjectsHandled' => 0, 
        'TotalStudents' => 0,
        'CoursesList' => 'No subjects assigned'
    ];
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
        /* CSS FIX: Puting text para sa Table at Metrics */
        header { position: relative; z-index: 1000 !important; }
        .faculty-main { position: relative; z-index: 1; }
        
        /* Ginawang puti ang text sa labels at numbers */
        .m-data .m-label { color: #ffffff !important; opacity: 0.9; }
        .m-data .m-number { color: #ffffff !important; font-size: 2.2rem; }
        
        /* Table enhancements para sa Visibility */
        .fac-table th { color: #ffffff !important; background: #043b68 !important; }
        .fac-table td { color: #333333 !important; } /* Maitim para mabasa sa puting card */
        
        .day-label { background: #043b68 !important; color: #ffffff !important; border-radius: 4px; padding: 5px 12px; }
        .time-highlight { color: #e67e22 !important; font-weight: 700; }
        
        .grading-note { font-size: 0.8rem; color: #555; margin-top: 5px; display: block; }
        
        /* Courses Display Box */
        .courses-box {
            margin-top: 15px;
            padding: 12px 15px;
            background: rgba(255,255,255,0.15);
            border-radius: 8px;
            border-left: 4px solid #e67e22;
        }
        
        .courses-box strong {
            font-size: 0.85rem;
            opacity: 0.8;
            display: block;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .courses-box span {
            font-size: 1rem;
            line-height: 1.6;
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
                
                <!-- Display Courses Handled -->
                <div class="courses-box">
                    <strong>📚 Current Courses Handled:</strong>
                    <span><?php echo htmlspecialchars($metrics['CoursesList']); ?></span>
                </div>
            </div>
        </section>

        <section class="metrics-grid">
            <div class="m-card" style="background: #e67e22;">
                <div class="m-icon" style="background: rgba(255,255,255,0.2);">📚</div>
                <div class="m-data">
                    <span class="m-label">Total Subjects</span>
                    <span class="m-number"><?php echo str_pad($metrics['SubjectsHandled'], 2, "0", STR_PAD_LEFT); ?></span>
                </div>
            </div>

            <div class="m-card" style="background: #16a085;">
                <div class="m-icon" style="background: rgba(255,255,255,0.2);">👨‍🎓</div>
                <div class="m-data">
                    <span class="m-label">Total Students</span>
                    <span class="m-number"><?php echo number_format($metrics['TotalStudents']); ?></span>
                </div>
            </div>

            <div class="m-card" style="background: #2980b9;">
                <div class="m-icon" style="background: rgba(255,255,255,0.2);">🕒</div>
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
                                    <td colspan="5" style="text-align:center; padding: 40px; color: #666;">
                                        <strong>No schedule records found.</strong><br>
                                        Please contact the registrar for subject assignments.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($schedules as $sched): ?>
                                <tr>
                                    <td><span class="day-label"><?php echo htmlspecialchars($sched['dayOfWeek'] ?? 'TBA'); ?></span></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($sched['subjectCode'] ?? ''); ?></strong> 
                                        - <?php echo htmlspecialchars($sched['subjectName'] ?? ''); ?>
                                    </td>
                                    <td><strong style="color: #043b68;"><?php echo htmlspecialchars($sched['sectionName'] ?? 'N/A'); ?></strong></td>
                                    <td class="time-highlight">
                                        <?php 
                                            $start = !empty($sched['StartTime']) ? date("h:i A", strtotime($sched['StartTime'])) : '00:00';
                                            $end = !empty($sched['EndTime']) ? date("h:i A", strtotime($sched['EndTime'])) : '00:00';
                                            echo $start . ' - ' . $end;
                                        ?>
                                    </td>
                                    <td><span style="font-weight: 600;"><?php echo htmlspecialchars($sched['room'] ?? 'TBA'); ?></span></td>
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

    <script src="../Professor_Dashboard/Header/ProfessorHeader.js"></script>
    <script src="ProfessorDashboard.js"></script>
</body>
</html>