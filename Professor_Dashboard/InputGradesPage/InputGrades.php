<?php
session_start();
require_once '../../Database/database_Connection.php'; 

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Professor') {
    header("Location: ../../Login_FacultyPage/loginFaculty.php");
    exit();
}

$professorID = $_SESSION['professorID'] ?? $_SESSION['user_id'];
$current_page = 'InputGrades.php';

$subjectID = $_GET['subjectID'] ?? 1; 

$students = [];
try {
    $stmt = $conn->prepare("{call sp_GetInputGradesList(?, ?)}");
    $stmt->execute([$professorID, $subjectID]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
} catch (Exception $e) {
    $students = []; 
}

// Function para sa initial remarks display
function getRemarks($grade) {
    $grade = strtoupper(trim($grade));
    if ($grade === "") return "";
    if ($grade === "INC") return "INCOMPLETE";
    if ($grade === "W") return "WITHDRAWN";
    
    $num = floatval($grade);
    if ($num >= 1.0 && $num <= 3.0) return "PASSED";
    if ($num > 3.0 && $num <= 5.0) return "FAILED";
    return "INVALID";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Grades - ISCP Faculty</title>
    <link rel="stylesheet" href="../Header/ProfessorHeader.css">
    <link rel="stylesheet" href="../ProfessorDashboard.css">
    <link rel="stylesheet" href="../../User_Dashboard/Footer/FooterDashboard.css">
    <link rel="stylesheet" href="InputGrades.css">
    <style>
        .remarks-col { font-weight: bold; font-size: 0.9rem; }
        .status-passed { color: #27ae60; }
        .status-failed { color: #e74c3c; }
        .status-inc { color: #e67e22; }
        .status-w { color: #7f8c8d; }
    </style>
</head>
<body>
    <?php include '../Header/ProfessorHeader.php'; ?>

    <main class="grades-container">
        <div class="glass-header-container">
            <header class="content-header">
                <div class="title-section">
                    <h1>Input Student Grades</h1>
                    <p>Professor ID: <span class="subject-highlight"><?php echo htmlspecialchars($professorID); ?></span></p>
                    <small style="color: #ccc;">Scale: 1.0 (Uno) to 5.0 (Failed) | INC | W</small>
                </div>
                <div class="action-section">
                    <input type="text" id="gradeSearch" placeholder="Search student name or ID...">
                    <button class="btn-save" id="downloadPdfBtn">📄 Download PDF</button>
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
                            <th class="text-center">Final Grade</th>
                            <th class="text-center">Remarks</th> <th class="text-center actions-col">Action</th>
                        </tr>
                    </thead>
                    <tbody id="gradeTableBody">
                        <?php if (empty($students)): ?>
                            <tr id="noResults"><td colspan="5" style="text-align:center; padding:30px;">No students assigned.</td></tr>
                        <?php else: ?>
                            <?php foreach ($students as $student): 
                                $currentGrade = $student['finalGrade'] ?? '';
                                $remarkText = getRemarks($currentGrade);
                                $remarkClass = "";
                                if ($remarkText == "PASSED") $remarkClass = "status-passed";
                                elseif ($remarkText == "FAILED") $remarkClass = "status-failed";
                                elseif ($remarkText == "INCOMPLETE") $remarkClass = "status-inc";
                                elseif ($remarkText == "WITHDRAWN") $remarkClass = "status-w";
                            ?>
                            <tr data-student-id="<?php echo htmlspecialchars($student['id']); ?>" 
                                data-subject-id="<?php echo $subjectID; ?>">
                                <td class="id-col"><strong><?php echo htmlspecialchars($student['id']); ?></strong></td>
                                <td class="name-col"><?php echo htmlspecialchars($student['name']); ?></td>
                                <td class="text-center">
                                    <input type="text" 
                                           class="grade-input" 
                                           maxlength="5" 
                                           placeholder="1.00"
                                           value="<?php echo htmlspecialchars($currentGrade); ?>">
                                </td>
                                <td class="text-center remarks-col <?php echo $remarkClass; ?>">
                                    <?php echo $remarkText; ?>
                                </td>
                                <td class="text-center actions-col">
                                    <button class="btn-row-save">Save</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <?php include '../../User_Dashboard/Footer/FooterDashboard.php'; ?>
    <script src="InputGrades.js"></script>
    <script src="/Professor_Dashboard/Header/ProfessorHeader.js"></script>
</body>
</html>