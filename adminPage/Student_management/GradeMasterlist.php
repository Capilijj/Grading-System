<?php
/**
 * adminPage/Student_Management/GradeMasterlist.php
 */
session_start();
require_once '../../Database/database_Connection.php'; 

// 1. SECURITY CHECK
if (!isset($_SESSION['role'])) {
    header("Location: ../../Login_FacultyPage/loginFaculty.php");
    exit();
}

$message = "";
$gradeRecords = []; // Initialize as empty array para iwas Fatal Error sa count()

// 2. UPDATE GRADE LOGIC
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_grade_master'])) {
    try {
        /**
         * FIX: Base sa "too many arguments" error, ginawa nating 4 parameters ang EXEC.
         * Format: StudentID, SubjectID, Grade, Remarks
         */
        $stmt = $conn->prepare("EXEC sp_UpdateStudentGrade ?, ?, ?, ?");
        
        $stmt->execute([
            $_POST['student_id'],
            $_POST['subject_id'],
            $_POST['new_grade'], 
            $_POST['remarks']
        ]);
        
        $message = "✅ Success: Grade for " . htmlspecialchars($_POST['student_id']) . " has been updated.";
    } catch (Exception $e) {
        $message = "❌ Error: " . $e->getMessage();
    }
}

// 3. SEARCH & FETCH LOGIC
$search = $_GET['search'] ?? null;
try {
    /**
     * Tandaan: Siguraduhin na sa loob ng SQL Server, ang procedure na ito 
     * ay tumutukoy sa table na 'Grade' (singular).
     */
    $stmt = $conn->prepare("EXEC sp_GetGradeMasterlist ?");
    $stmt->execute([$search]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($result) {
        $gradeRecords = $result;
    }
} catch (PDOException $e) {
    $message = "❌ Database Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Masterlist | ISCP Admin</title>
    
    <link rel="stylesheet" href="../sidebar.css"> 
    <link rel="stylesheet" href="Studentmanagement.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { 
            display: flex; 
            background-color: #f4f7f6; 
            margin: 0; 
            font-family: 'Inter', sans-serif;
        }
        
        .main-content { 
            flex: 1; 
            margin-left: 260px; /* Sukat ng sidebar */
            padding: 2rem; 
            min-height: 100vh; 
            transition: all 0.3s ease; 
        }

        /* UI Components */
        .alert-box { 
            padding: 15px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            border-left: 5px solid; 
            font-size: 0.9rem;
        }
        .alert-error { background: #f8d7da; color: #721c24; border-color: #f5c6cb; }
        .alert-success { background: #d4edda; color: #155724; border-color: #27ae60; }

        .grade-input-small { 
            width: 70px; 
            padding: 8px; 
            border-radius: 5px; 
            border: 1px solid #ddd; 
            text-align: center; 
            font-weight: bold; 
            outline: none;
        }
        .grade-input-small:focus { border-color: #0c225e; box-shadow: 0 0 5px rgba(12,34,94,0.2); }

        .remarks-select { 
            padding: 8px; 
            border-radius: 5px; 
            border: 1px solid #ddd; 
            font-size: 0.85rem; 
            background: white;
        }

        .btn-update {
            background: #27ae60; 
            color: white; 
            border: none; 
            padding: 8px 18px; 
            border-radius: 6px; 
            cursor: pointer; 
            font-weight: 600;
            transition: background 0.2s;
        }
        .btn-update:hover { background: #219150; }

        @media (max-width: 768px) { 
            .main-content { margin-left: 0; padding: 1rem; } 
        }
    </style>
</head>
<body>

    <?php include '../sidebar.php'; ?>
    
    <main class="main-content">
        <header class="top-header">
            <div class="header-left">
                <h1>Grade Masterlist</h1>
                <p>Monitor and update student academic performance.</p>
            </div>
            
            <div class="header-right">
                <form method="GET" style="display: flex; gap: 10px;">
                    <input type="text" name="search" placeholder="Search Student or Subject..." 
                           value="<?= htmlspecialchars($search ?? '') ?>" 
                           style="padding: 10px; border-radius: 8px; border: 1px solid #ddd; width: 280px;">
                    <button type="submit" style="padding: 10px 20px; background: #0c225e; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight:600;">Search</button>
                </form>
            </div>
        </header>

        <?php if($message): ?>
            <div class="alert-box <?= strpos($message, '✅') !== false ? 'alert-success' : 'alert-error' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <div class="table-container-wide glass-card">
            <table class="full-info-table">
                <thead>
                    <tr>
                        <th>Student Information</th>
                        <th>Subject & Course</th>
                        <th>Instructor</th>
                        <th>Final Grade</th>
                        <th>Remarks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($gradeRecords) > 0): ?>
                        <?php foreach ($gradeRecords as $row): ?>
                        <tr>
                            <form method="POST">
                                <input type="hidden" name="student_id" value="<?= htmlspecialchars($row['studentID'] ?? '') ?>">
                                <input type="hidden" name="subject_id" value="<?= htmlspecialchars($row['subjectID'] ?? '') ?>">

                                <td>
                                    <strong><?= htmlspecialchars($row['StudentName'] ?? 'Unknown Student') ?></strong><br>
                                    <code style="color: #666;"><?= htmlspecialchars($row['studentID'] ?? 'N/A') ?></code>
                                </td>
                                <td>
                                    <span style="font-weight: 600; color: #0c225e;"><?= htmlspecialchars($row['subjectName'] ?? 'N/A') ?></span><br>
                                    <small class="text-muted"><?= htmlspecialchars($row['courseName'] ?? 'N/A') ?></small>
                                </td>
                                <td>
                                    <span class="prof-name">Prof. <?= htmlspecialchars($row['ProfessorName'] ?? 'N/A') ?></span>
                                </td>
                                <td>
                                    <input type="text" name="new_grade" value="<?= htmlspecialchars($row['finalGrade'] ?? '') ?>" class="grade-input-small">
                                </td>
                                <td>
                                    <select name="remarks" class="remarks-select">
                                        <?php 
                                            $current_remark = $row['remarks'] ?? '';
                                            $options = ['P' => 'Passed', 'F' => 'Failed', 'INC' => 'Incomplete', 'W' => 'Withdrawn', 'ENROLLED' => 'Enrolled'];
                                            foreach($options as $val => $label):
                                        ?>
                                            <option value="<?= $val ?>" <?= $current_remark == $val ? 'selected' : '' ?>><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <button type="submit" name="update_grade_master" class="btn-update">
                                        Update
                                    </button>
                                </td>
                            </form>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding: 3rem; color: #888;">
                                <div style="font-size: 1.2rem; margin-bottom: 10px;">📭 No records found.</div>
                                <small>Verify if the table <b>'Grade'</b> contains data in your SQL Server.</small>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>