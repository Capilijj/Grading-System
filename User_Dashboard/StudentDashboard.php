<?php
session_start();

/** * 1. DATABASE CONNECTION & AUTH CHECK
 */
// Ginagamit ang __DIR__ para masiguro ang path mula sa current directory patungo sa Database folder
require_once __DIR__ . '/../Database/database_Connection.php'; 

// Prevent caching para laging updated ang data na nakikita ng student
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Check kung ang user ay naka-log in at kung siya ay isang Student
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Student') {
    header("Location: ../Login_StudentPage/loginStudent.php");
    exit();
}

$studentID = $_SESSION['studentID'] ?? '';

/**
 * 2. FETCH STUDENT DETAILS (NAME & ID)
 */
try {
    $stmt_details = $conn->prepare("{call sp_GetStudentDetails(?)}");
    $stmt_details->execute([$studentID]);
    $student = $stmt_details->fetch(PDO::FETCH_ASSOC);
    $stmt_details->closeCursor();

    if ($student) {
        $mInitial = !empty($student['mName']) ? substr($student['mName'], 0, 1) . "." : "";
        $fullName = strtoupper($student['lName'] . ", " . $student['fName'] . " " . $mInitial);
    } else {
        $fullName = "STUDENT NOT FOUND";
    }
} catch (Exception $e) {
    $fullName = "ERROR LOADING NAME";
}

/**
 * 3. FETCH DASHBOARD STATS (GPA, UNITS, INC)
 * Tandaan: Ang logic para sa "8 subjects minimum bago lumabas ang GPA" ay naka-set sa Stored Procedure.
 */
try {
    $stmt_stats = $conn->prepare("{call sp_GetStudentDashboardStats(?)}");
    $stmt_stats->execute([$studentID]);
    $stats = $stmt_stats->fetch(PDO::FETCH_ASSOC) ?: ['GPA' => '0.00', 'TotalUnits' => '0', 'INC_Count' => '0'];
    $stmt_stats->closeCursor();
} catch (Exception $e) {
    $stats = ['GPA' => '0.00', 'TotalUnits' => '0', 'INC_Count' => '0'];
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

            <section class="stats-grid">
                <div class="card orange-card">
                    <p>Current GPA</p>
                    <div class="card-value">
                        <?php echo htmlspecialchars($stats['GPA']); ?>
                    </div>
                    <?php if ($stats['GPA'] == '0.00'): ?>
                        <small style="font-size: 0.65rem; display: block; margin-top: 5px; color: #fff;">
                            *Available after 8 subjects are graded
                        </small>
                    <?php endif; ?>
                </div>

                <div class="card teal-card">
                    <p>Total Units Enrolled</p>
                    <div class="card-value"><?php echo htmlspecialchars($stats['TotalUnits']); ?></div>
                </div>

                <div class="card teal-card">
                    <p>Incomplete Grades</p>
                    <div class="card-value"><?php echo htmlspecialchars($stats['INC_Count']); ?></div>
                </div>

                <div class="card teal-card">
                    <p>Account Balance</p>
                    <div class="card-value small-text">Free-Educ</div>
                </div>
            </section>
        </div>

        <section class="white-content-container">
            <div class="guidelines-header">
                ISCP ACADEMIC COMPLIANCE GUIDELINES (ACG)
            </div>
            
            <div class="guidelines-content">
                <p class="intro-text">These are the guidelines for the ISCP (Institute of Scholars and Certified Professionals) governing the maintenance of eligibility and benefits under the Free Education Law (e.g., RA 10931).</p>
                
                <div class="rule-block">
                    <h4>I. ELIGIBILITY AND QUALIFICATION</h4>
                    <p>All students who have satisfied the admission requirements of ISCP and do not fall into the "ineligible" categories stated in the Free Higher Education (FHE) Act are entitled to avail of Free Education.</p>
                </div>

                <div class="rule-block">
                    <h4>2. SCHOLASTIC DELINQUENCY MATRIX</h4>
                    <p class="sub-text">To continuously enjoy the Free Education benefits, a student must maintain a Good Scholastic Standing.</p>
                    
                    <p class="table-title">FOR STUDENTS WITH A LOAD OF 22 UNITS AND ABOVE IN THE PREVIOUS SEMESTER</p>
                    
                    <table class="delinquency-table">
                        <thead>
                            <tr>
                                <th>Number of Subject Failed/ Withdrawn/ Dropped</th>
                                <th>Action to be Taken</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1 subject</td><td>Verbal Warning</td><td>Requires mandatory consultation with the Program Coordinator.</td>
                            </tr>
                            <tr>
                                <td>2 subjects</td><td>Written Warning</td><td>Scholarship remains, but must pass all subjects in the current semester.</td>
                            </tr>
                            <tr>
                                <td>3 subjects</td><td>Probation</td><td>Scholarship will be forfeited for the following semester.</td>
                            </tr>
                            <tr>
                                <td>4 subjects or more</td><td>Dismissal</td><td>Deemed Dropped from the University.</td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="note">Note: A student who acquires a "Written Warning" for dalawang (2) non-consecutive semesters, the Free Education Scholarship Grant will be automatically forfeited.</p>
                </div>

                <div class="rule-block">
                    <h4>3. MAXIMUM RESIDENCY AND YEARS OF STAY</h4>
                    <p>If a student stays beyond the allowed number of years covered by the Free Education Law for their course, they shall pay the tuition and miscellaneous fees for the remaining allowable years of stay.</p>
                </div>
            </div>
        </section>
    </main>

    <?php include 'Footer/FooterDashboard.php'; ?>
    <script src="Header/header.js"></script>
</body>
</html>