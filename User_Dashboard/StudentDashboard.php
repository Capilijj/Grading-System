<?php
session_start();

require_once __DIR__ . '/../Database/database_Connection.php'; 

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Student') {
    header("Location: ../Login_StudentPage/loginStudent.php");
    exit();
}

$studentID = $_SESSION['studentID'] ?? '';

/**
 * 2. FETCH STUDENT DETAILS (including studentType)
 */
try {
    $stmt_details = $conn->prepare("{call sp_GetStudentDetails(?)}");
    $stmt_details->execute([$studentID]);
    $student = $stmt_details->fetch(PDO::FETCH_ASSOC);
    $stmt_details->closeCursor();

    if ($student) {
        $mInitial = !empty($student['mName']) ? substr($student['mName'], 0, 1) . "." : "";
        $fullName = strtoupper($student['lName'] . ", " . $student['fName'] . " " . $mInitial);
        $studentType = $student['studentType'] ?? 'Scholar'; // Get student type from DB
    } else {
        $fullName = "STUDENT NOT FOUND";
        $studentType = "N/A";
    }
} catch (Exception $e) {
    $fullName = "ERROR LOADING NAME";
    $studentType = "N/A";
}

/**
 * 3. FETCH DASHBOARD STATS (GPA & INC) - Using updated SP
 */
try {
    $stmt_stats = $conn->prepare("{call sp_GetStudentDashboardStats(?)}");
    $stmt_stats->execute([$studentID]);
    $stats = $stmt_stats->fetch(PDO::FETCH_ASSOC) ?: ['GPA' => '0.00', 'INC_Count' => '0'];
    $stmt_stats->closeCursor();
} catch (Exception $e) {
    $stats = ['GPA' => '0.00', 'INC_Count' => '0'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - ISCP</title>

    <link rel="stylesheet" href="Header/header.css">
    <link rel="stylesheet" href="StudentDashboard.css">
    <link rel="stylesheet" href="Footer/FooterDashboard.css">
    <style>
        .rule-section-title { color: #0c225e; border-bottom: 2px solid #e67e22; padding-bottom: 10px; margin-top: 40px; text-transform: uppercase; letter-spacing: 1px; }
        .sub-rule { margin-left: 20px; border-left: 3px solid #eee; padding-left: 15px; margin-bottom: 20px; }
        .important-note { background-color: #fff3cd; border-left: 5px solid #ffc107; padding: 15px; margin: 20px 0; font-style: italic; }
        .delinquency-table { margin-top: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <?php include 'Header/header.php'; ?>

    <main class="dashboard-container">
        
        <section class="hero-section">
            <div class="hero-overlay">
                <div class="hero-content">
                    <h2>EMPOWERING SCHOOLS</h2>
                    <h3>WITH FAST AND ACCURATE GRADE</h3>
                    <h4>MANAGEMENT.</h4>
                    <p>Innovative Education, One Click at a Time.</p>
                </div>
            </div>
        </section>

        <div class="white-content-container floating-top">
            <div class="user-info-bar">
                Welcome, <strong><?php echo htmlspecialchars($fullName); ?></strong> (<?php echo htmlspecialchars($studentID); ?>)
            </div>

            <section class="stats-grid" style="grid-template-columns: repeat(3, 1fr);">
                <div class="card orange-card">
                    <p>Current GPA</p>
                    <div class="card-value"><?php echo htmlspecialchars($stats['GPA']); ?></div>
                    <?php if ($stats['GPA'] == '0.00'): ?>
                        <small style="font-size: 0.65rem; display: block; margin-top: 5px; color: #fff;">*Available after all subjects are graded</small>
                    <?php endif; ?>
                </div>

                <div class="card teal-card">
                    <p>Incomplete Grades</p>
                    <div class="card-value"><?php echo htmlspecialchars($stats['INC_Count']); ?></div>
                </div>

                <div class="card teal-card">
                    <p>Student Type</p>
                    <div class="card-value small-text"><?php echo htmlspecialchars($studentType); ?></div>
                </div>
            </section>
        </div>

        <section class="white-content-container">
            <div class="guidelines-header">
                ISCP ACADEMIC COMPLIANCE GUIDELINES (ACG) - v.2026
            </div>
            
            <div class="guidelines-content">
                <p class="intro-text">The following guidelines govern the maintenance of academic standing and scholarship eligibility at the Institute of Scholars and Certified Professionals (ISCP) in accordance with the Free Higher Education Act.</p>
                
                <h3 class="rule-section-title">I. Eligibility and General Qualifications</h3>
                <div class="sub-rule">
                    <p><strong>1.1 Admission:</strong> All students who have officially satisfied the admission and enrollment requirements of ISCP are eligible for the Free Education program.</p>
                    <p><strong>1.2 Duration:</strong> The benefit covers the prescribed period of the course (e.g., 4 years for BSCS) plus a one-year grace period if necessary.</p>
                    <p><strong>1.3 Residency:</strong> Students must maintain continuous residency. Any Leave of Absence (LOA) must be formally approved by the Registrar.</p>
                </div>

                <h3 class="rule-section-title">II. Scholastic Delinquency Matrix</h3>
                <p class="sub-text">Academic standing is evaluated at the end of every semester. Failure to meet the minimum passing rate will result in the following actions:</p>
                
                <table class="delinquency-table">
                    <thead>
                        <tr>
                            <th>Number of Subjects Failed</th>
                            <th>Action to be Taken</th>
                            <th>Impact on Scholarship</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1 Subject</td>
                            <td>Verbal Warning</td>
                            <td>Retention with mandatory peer-tutoring session.</td>
                        </tr>
                        <tr>
                            <td>2 Subjects</td>
                            <td>Written Warning</td>
                            <td>Academic probation; must pass 100% of next sem load.</td>
                        </tr>
                        <tr>
                            <td>3 Subjects</td>
                            <td>Final Probation</td>
                            <td>Scholarship benefits suspended for one (1) semester.</td>
                        </tr>
                        <tr>
                            <td>4+ Subjects</td>
                            <td>Dismissal</td>
                            <td>Permanent disqualification from the University.</td>
                        </tr>
                    </tbody>
                </table>

                <div class="important-note">
                    <strong>Note:</strong> A "Written Warning" issued for two (2) non-consecutive semesters will automatically trigger a Scholarship Forfeiture. Grades of "Incomplete" (INC) must be settled within one academic year.
                </div>

                <h3 class="rule-section-title">III. Maximum Residency Policy</h3>
                <div class="sub-rule">
                    <p><strong>3.1 Extension:</strong> Students who fail to graduate within the prescribed timeframe plus the one-year grace period will be required to pay full tuition and miscellaneous fees for the succeeding semesters.</p>
                    <p><strong>3.2 Shifting:</strong> Shifting to another course is allowed once. The years spent in the previous course will be deducted from the total years of eligibility in the new course.</p>
                </div>

                <h3 class="rule-section-title">IV. Conduct and Behavioral Standards</h3>
                <div class="sub-rule">
                    <p>Students must maintain a clean disciplinary record. Any major offense as defined in the Student Manual (e.g., Academic Dishonesty, Vandalism, Bullying) is grounds for immediate termination of scholarship and expulsion from the Institute.</p>
                </div>
            </div>
        </section>
    </main>

    <?php include 'Footer/FooterDashboard.php'; ?>
    <script src="Header/header.js"></script>
</body>
</html>