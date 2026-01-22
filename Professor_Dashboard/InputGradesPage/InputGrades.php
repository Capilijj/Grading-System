<?php
$current_page = 'InputGrades.php';
// Placeholder Data ng mga Estudyante
$students = [
    ['id' => '2023-1001', 'name' => 'Aguas, Maria Clara', 'prelim' => '', 'midterm' => '', 'final' => ''],
    ['id' => '2023-1002', 'name' => 'Dela Cruz, Juan', 'prelim' => '1.25', 'midterm' => '1.50', 'final' => ''],
    ['id' => '2023-1005', 'name' => 'Luna, Antonio', 'prelim' => '2.00', 'midterm' => '', 'final' => ''],
    ['id' => '2023-1010', 'name' => 'Rizal, Jose', 'prelim' => '1.00', 'midterm' => '1.00', 'final' => '1.00'],
];
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
</head>
<body>

    <?php include '../Header/ProfessorHeader.php'; ?>

    <main class="grades-container">
        <div class="glass-header-container">
            <header class="content-header">
                <div class="title-section">
                    <h1>Input Student Grades</h1>
                    <p>Subject: <span class="subject-highlight">IT101 - Intro Computing (Section 1C)</span></p>
                </div>
                <div class="action-section">
                    <input type="text" id="gradeSearch" placeholder="Search student name or ID...">
                    <button class="btn-save">💾 Save All Grades</button>
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
                        </tr>
                    </thead>
                    <tbody id="gradeTableBody">
                        <?php foreach ($students as $student): ?>
                        <tr>
                            <td class="id-col"><strong><?php echo $student['id']; ?></strong></td>
                            <td class="name-col"><?php echo $student['name']; ?></td>
                            <td class="text-center"><input type="number" step="0.25" class="grade-input" value="<?php echo $student['prelim']; ?>" placeholder="-"></td>
                            <td class="text-center"><input type="number" step="0.25" class="grade-input" value="<?php echo $student['midterm']; ?>" placeholder="-"></td>
                            <td class="text-center"><input type="number" step="0.25" class="grade-input" value="<?php echo $student['final']; ?>" placeholder="-"></td>
                            <td class="text-center"><strong class="final-grade">--</strong></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr id="noResults" style="display: none;">
                            <td colspan="6" style="text-align: center; padding: 30px; color: #666;">No students found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <?php include '../../User_Dashboard/Footer/FooterDashboard.php'; ?>

    <script src="../Header/ProfessorHeader.js"></script>
    <script src="InputGrades.js"></script>
</body>
</html>