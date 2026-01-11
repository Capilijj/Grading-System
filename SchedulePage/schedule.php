<?php
/**
 * AUTOMATED ACADEMIC YEAR & SEMESTER LOGIC
 * Kusang nagbabago base sa buwan at taon ng server.
 */
$currentMonth = (int)date('n'); 
$currentYear = (int)date('Y');

if ($currentMonth >= 6 && $currentMonth <= 11) {
    // June to November typically 1st Sem
    $semester = "First Semester";
    $academicYear = $currentYear . "-" . ($currentYear + 1);
} else {
    // December to May typically 2nd Sem
    $semester = "Second Semester";
    if ($currentMonth >= 1 && $currentMonth <= 5) {
        $academicYear = ($currentYear - 1) . "-" . $currentYear;
    } else {
        $academicYear = $currentYear . "-" . ($currentYear + 1);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Schedule - ISCP</title>
    
    <link rel="stylesheet" href="../Header_Dashboard/header.css">
    <link rel="stylesheet" href="schedule.css">
    <link rel="stylesheet" href="../Footer_Dashboard/FooterDashboard.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>
<body>

    <?php include '../Header_Dashboard/header.php'; ?>

    <div id="schedule-content">
        <main class="schedule-container">
            <div class="white-content-container">
                
                <div class="page-title-inside">
                    <h2>MY CLASS SCHEDULE</h2>
                    <p>Academic Year <?php echo $academicYear; ?> | <?php echo $semester; ?></p>
                </div>

                <div class="schedule-header">
                    <span>Weekly Class Schedule</span>
                    <button onclick="downloadPDF()" class="print-btn btn-download">
                        Download Schedule 📥
                    </button>
                </div>
                
                <div class="schedule-table-wrapper">
                    <table class="schedule-table">
                        <thead>
                            <tr>
                                <th>Subject Code</th>
                                <th>Description</th>
                                <th>Units</th>
                                <th>Day</th>
                                <th>Time</th>
                                <th>Room</th>
                                <th>Instructor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                             /* ---------------------------------------------------------
                                Ken! Dito mo na isaksak yung sa database:
                                ai lang to HAHA.
                                1. Connect mo muna yung database natin.
                                2. Hatakin mo yung schedule nung student gamit yung ID 
                                   o Session Number nila (SELECT * FROM schedule WHERE student_id = ...).
                                3. I-loop mo lang dito gamit 'while' or 'foreach' para 
                                   kusa nang lumabas yung mga rows sa table.
                                  
                                Sample na galawan:
                                  while($row = $result->fetch_assoc()) {
                                      echo "<tr>
                                              <td>" . $row['sub_code'] . "</td>
                                              <td>" . $row['description'] . "</td>
                                              <td>" . $row['units'] . "</td>
                                              <td>" . $row['day'] . "</td>
                                              <td>" . $row['time'] . "</td>
                                              <td>" . $row['room'] . "</td>
                                              <td>" . $row['instructor'] . "</td>
                                            </tr>";
                                  }
                                  
                                Note: Pakitanggal nalang yung static <tr> sa baba pag okay na loop mo.
                                Ikaw na bahala sa query, Ken! 
                                --------------------------------------------------------- */
                            ?>
                            
                            <tr>
                                <td>IT101</td>
                                <td>Introduction to Computing</td>
                                <td>3</td>
                                <td>Mon/Wed</td>
                                <td>08:00 AM - 10:00 AM</td>
                                <td>CL-1</td>
                                <td>Prof. Dela Cruz</td>
                            </tr>
                            <tr>
                                <td>CS202</td>
                                <td>Data Structures and Algorithms</td>
                                <td>3</td>
                                <td>Tue/Thu</td>
                                <td>01:00 PM - 03:00 PM</td>
                                <td>CL-2</td>
                                <td>Prof. Santos</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <?php include '../Footer_Dashboard/FooterDashboard.php'; ?>

    <script src="../Header_Dashboard/header.js"></script>
    <script src="schedule.js"></script>
</body>
</html>