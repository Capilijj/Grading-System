<?php
$current_page = 'Classlist.php';
$students = [
    ['id' => '2023-1001', 'name' => 'Aguas, Maria Clara', 'course' => 'BSCS', 'year' => '2nd Year', 'status' => 'Regular'],
    ['id' => '2023-1002', 'name' => 'Dela Cruz, Juan', 'course' => 'BSCS', 'year' => '2nd Year', 'status' => 'Regular'],
    ['id' => '2023-1005', 'name' => 'Luna, Antonio', 'course' => 'BSCS', 'year' => '2nd Year', 'status' => 'Irregular'],
    ['id' => '2023-1010', 'name' => 'Rizal, Jose', 'course' => 'BSCS', 'year' => '2nd Year', 'status' => 'Regular'],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class List - ISCP Faculty</title>
    
    <link rel="stylesheet" href="../Header/ProfessorHeader.css">
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
                    <p>Subject: <span class="subject-highlight">CS202 - Data Structures (Section 2A)</span></p>
                </div>
                <div class="action-section">
                    <input type="text" id="studentSearch" placeholder="Search student name or ID...">
                    <button class="btn-export">📊 Export List</button>
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
                            <th>Course</th>
                            <th>Year Level</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="studentTableBody">
                        <?php foreach ($students as $student): ?>
                        <tr>
                            <td class="id-col"><strong><?php echo $student['id']; ?></strong></td>
                            <td class="name-col"><?php echo $student['name']; ?></td>
                            <td><?php echo $student['course']; ?></td>
                            <td><?php echo $student['year']; ?></td>
                            <td>
                                <span class="tag-status <?php echo strtolower($student['status']); ?>">
                                    <?php echo $student['status']; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr id="noResults" style="display: none;">
                            <td colspan="5" style="text-align: center; padding: 30px; color: #666;">No students found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <?php include '../../User_Dashboard/Footer/FooterDashboard.php'; ?>

    <script src="../Header/ProfessorHeader.js"></script>
    <script src="Classlist.js"></script>
</body>
</html>