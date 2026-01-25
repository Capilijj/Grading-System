<?php
/**
 * Classlist.php - Updated Dynamic Version
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Database Connection
$connectionFile = __DIR__ . '/../../Database/database_Connection.php';
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
    header("Location: ../../Login_FacultyPage/loginFaculty.php");
    exit();
}

$professorID = $_SESSION['professorID'] ?? $_SESSION['user_id'];
$current_page = 'Classlist.php';

// Kuhanin ang subjectID mula sa URL (e.g., Classlist.php?subjectID=1)
// Default ay NULL para ipakita ang lahat kung walang napili
$subjectID = $_GET['subjectID'] ?? null; 

// 3. Fetch Students from DB
$students = [];
try {
    // In-update para tumanggap ng dalawang parameter: ProfID at SubjectID
    $stmt = $conn->prepare("{call sp_GetClasslist(?, ?)}");
    $stmt->execute([$professorID, $subjectID]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
} catch (Exception $e) {
    $students = []; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class List - ISCP Faculty</title>
    
    <link rel="stylesheet" href="/Professor_Dashboard/Header/ProfessorHeader.css">
    <link rel="stylesheet" href="../ProfessorDashboard.css">
    <link rel="stylesheet" href="../../User_Dashboard/Footer/FooterDashboard.css">
    <link rel="stylesheet" href="Classlist.css">
</head>
<body>

    <?php include '../Header/ProfessorHeader.php'; ?>

    <main class="classlist-container">
        <div class="glass-header-container">
            <header class="content-header">
                <div class="title-section">
                    <h1>Class List</h1>
                    <?php if ($subjectID): ?>
                        <p>Subject ID: <span class="subject-highlight"><?php echo htmlspecialchars($subjectID); ?></span></p>
                    <?php endif; ?>
                    <p>Total Students: <span class="subject-highlight"><?php echo count($students); ?></span></p>
                </div>
                <div class="action-section">
                    <input type="text" id="studentSearch" placeholder="Search student name or ID...">
                    <button class="btn-export" onclick="window.print()">📊 Print List</button>
                </div>
            </header>
        </div>

        <section class="table-card">
            <div class="table-wrapper">
                <table class="main-table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Section/Course</th>
                            <th>Year Level</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="studentTableBody">
                        <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px; color: #666;">
                                    No students found for this subject.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($students as $student): ?>
                            <tr>
                                <td class="id-col"><strong><?php echo htmlspecialchars($student['id']); ?></strong></td>
                                <td class="name-col"><?php echo htmlspecialchars($student['name']); ?></td>
                                <td><?php echo htmlspecialchars($student['course'] ?? $student['section'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($student['year']); ?></td>
                                <td>
                                    <span class="tag-status <?php echo strtolower($student['status'] ?? 'regular'); ?>">
                                        <?php echo htmlspecialchars($student['status'] ?? 'Regular'); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <tr id="noResults" style="display: none;">
                            <td colspan="5" style="text-align: center; padding: 30px; color: #666;">No matches found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <?php include '../../User_Dashboard/Footer/FooterDashboard.php'; ?>

    <script src="/Professor_Dashboard/Header/ProfessorHeader.js"></script>
    <script src="Classlist.js"></script>

</body>
</html>