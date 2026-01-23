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

$students = [];
try {
    // Tinitiyak na ang SP ay nagbabalik ng DISTINCT records
    $stmt = $conn->prepare("{call sp_GetInputGradesList(?)}");
    $stmt->execute([$professorID]);
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
    <title>Input Grades - ISCP Faculty</title>
    <link rel="stylesheet" href="../Header/ProfessorHeader.css">
    <link rel="stylesheet" href="../ProfessorDashboard.css">
    <link rel="stylesheet" href="../../User_Dashboard/Footer/FooterDashboard.css">
    <link rel="stylesheet" href="InputGrades.css">
    <style>
        /* Green PDF Button */
        #downloadPdfBtn {
            background-color: #27ae60 !important;
            border-color: #219150 !important;
            color: white !important;
        }
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
                            <th class="text-center">Prelim</th>
                            <th class="text-center">Midterm</th>
                            <th class="text-center">Finals</th>
                            <th class="text-center">Average</th>
                            <th class="text-center actions-col">Action</th>
                        </tr>
                    </thead>
                    <tbody id="gradeTableBody">
                        <?php if (empty($students)): ?>
                            <tr id="noResults"><td colspan="7" style="text-align:center; padding:30px;">No students assigned.</td></tr>
                        <?php else: ?>
                            <?php foreach ($students as $student): ?>
                            <tr data-student-id="<?php echo htmlspecialchars($student['id']); ?>">
                                <td class="id-col"><strong><?php echo htmlspecialchars($student['id']); ?></strong></td>
                                <td class="name-col"><?php echo htmlspecialchars($student['name']); ?></td>
                                <td class="text-center"><input type="number" step="0.25" class="grade-input" value="<?php echo $student['prelim'] > 0 ? $student['prelim'] : ''; ?>"></td>
                                <td class="text-center"><input type="number" step="0.25" class="grade-input" value="<?php echo $student['midterm'] > 0 ? $student['midterm'] : ''; ?>"></td>
                                <td class="text-center"><input type="number" step="0.25" class="grade-input" value="<?php echo $student['finals'] > 0 ? $student['finals'] : ''; ?>"></td>
                                <td class="text-center"><strong class="final-grade">--</strong></td>
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
</body>
</html>