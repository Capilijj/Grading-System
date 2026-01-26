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

// FIXED: Use existing stored procedure to get professor's subjects
$subjectID = $_GET['subjectID'] ?? null; 

// Get all subjects assigned to this professor using sp_GetProfessorSchedule
$professorSubjects = [];
try {
    $stmt = $conn->prepare("{call sp_GetProfessorSchedule(?)}");
    $stmt->execute([$professorID]);
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    
    // Extract unique subjects from schedule
    $uniqueSubjects = [];
    foreach ($schedules as $sched) {
        $subjID = $sched['subjectID'];
        if (!isset($uniqueSubjects[$subjID])) {
            $uniqueSubjects[$subjID] = [
                'subjectID' => $sched['subjectID'],
                'subjectCode' => $sched['subjectCode'],
                'subjectName' => $sched['subjectName'],
                'sectionName' => $sched['sectionName']
            ];
        }
    }
    $professorSubjects = array_values($uniqueSubjects);
    
} catch (Exception $e) {
    $professorSubjects = [];
}

// If no subjectID selected and professor has subjects, use the first one
if (!$subjectID && !empty($professorSubjects)) {
    $subjectID = $professorSubjects[0]['subjectID'];
}

// Get students for the selected subject
$students = [];
$subjectInfo = null;

if ($subjectID) {
    try {
        $stmt = $conn->prepare("{call sp_GetInputGradesList(?, ?)}");
        $stmt->execute([$professorID, $subjectID]);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        
        // Get subject info for display
        foreach ($professorSubjects as $subject) {
            if ($subject['subjectID'] == $subjectID) {
                $subjectInfo = $subject;
                break;
            }
        }
    } catch (Exception $e) {
        $students = []; 
    }
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
        
        /* Subject Selector Styling */
        .subject-selector {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 15px;
        }
        
        .subject-selector label {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        .subject-selector select {
            padding: 10px 15px;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 8px;
            background: rgba(255,255,255,0.9);
            font-size: 0.9rem;
            min-width: 350px;
            cursor: pointer;
            transition: 0.3s;
        }
        
        .subject-selector select:focus {
            outline: none;
            background: #ffffff;
            box-shadow: 0 0 10px rgba(74, 144, 226, 0.5);
        }
        
        .current-subject-info {
            margin-top: 10px;
            padding: 8px 12px;
            background: rgba(255,255,255,0.15);
            border-radius: 6px;
            display: inline-block;
        }
        
        .current-subject-info small {
            color: rgba(255,255,255,0.85);
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
                    
                    <!-- Subject Selector Dropdown -->
                    <?php if (!empty($professorSubjects)): ?>
                    <div class="subject-selector">
                        <label for="subjectSelector">📚 Select Subject:</label>
                        <select id="subjectSelector" onchange="changeSubject(this.value)">
                            <?php foreach ($professorSubjects as $subject): ?>
                                <option value="<?php echo htmlspecialchars($subject['subjectID']); ?>" 
                                        <?php echo ($subject['subjectID'] == $subjectID) ? 'selected' : ''; ?>>
                                    <?php 
                                    echo htmlspecialchars($subject['subjectCode'] . ' - ' . $subject['subjectName']);
                                    if (!empty($subject['sectionName'])) {
                                        echo ' (' . htmlspecialchars($subject['sectionName']) . ')';
                                    }
                                    ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <?php if ($subjectInfo): ?>
                    <div class="current-subject-info">
                        <small>
                            Currently viewing: <strong><?php echo htmlspecialchars($subjectInfo['subjectCode']); ?></strong>
                            | Students: <strong><?php echo count($students); ?></strong>
                        </small>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                    
                    <small style="color: #ccc; display: block; margin-top: 10px;">
                        Scale: 1.0 (Uno) to 5.0 (Failed) | INC | W
                    </small>
                </div>
                <div class="action-section">
                    <input type="text" id="gradeSearch" placeholder="Search student name or ID...">
                    <button class="btn-save" id="downloadPdfBtn">📄 Download PDF</button>
                </div>
            </header>
        </div>

        <section class="table-card">
            <div class="table-wrapper">
                <?php if (empty($professorSubjects)): ?>
                    <div style="text-align:center; padding:50px; color:#666;">
                        <h3>📚 No Subjects Assigned</h3>
                        <p>You currently have no subjects assigned. Please contact the registrar.</p>
                    </div>
                <?php else: ?>
                    <table class="main-table">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Full Name</th>
                                <th class="text-center">Final Grade</th>
                                <th class="text-center">Remarks</th>
                                <th class="text-center actions-col">Action</th>
                            </tr>
                        </thead>
                        <tbody id="gradeTableBody">
                            <?php if (empty($students)): ?>
                                <tr id="noResults">
                                    <td colspan="5" style="text-align:center; padding:30px; color:#666;">
                                        <strong>No students enrolled in this subject.</strong>
                                        <br><small>Make sure students are enrolled in <?php echo htmlspecialchars($subjectInfo['subjectCode'] ?? 'this subject'); ?></small>
                                    </td>
                                </tr>
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
                                    data-subject-id="<?php echo htmlspecialchars($subjectID); ?>">
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
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include '../../User_Dashboard/Footer/FooterDashboard.php'; ?>
    
    <script>
        // Function to change subject and reload page
        function changeSubject(subjectID) {
            if (subjectID) {
                window.location.href = 'InputGrades.php?subjectID=' + subjectID;
            }
        }
    </script>
    <script src="InputGrades.js"></script>
    <script src="/Professor_Dashboard/Header/ProfessorHeader.js"></script>
</body>
</html>