<?php
/**
 * AUTOMATED ACADEMIC YEAR & SEMESTER LOGIC
 */
$currentMonth = (int)date('n'); 
$currentYear = (int)date('Y');

if ($currentMonth >= 6 && $currentMonth <= 11) {
    $semester = "First Semester";
    $academicYear = $currentYear . "-" . ($currentYear + 1);
} else {
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
    <title>Grades - ISCP</title>
    
    <link rel="stylesheet" href="../StudentHeader/header.css">
    <link rel="stylesheet" href="grade.css">
    <link rel="stylesheet" href="../Footer_Dashboard/FooterDashboard.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>
<body>

    <?php include '../StudentHeader/header.php'; ?>

    <div id="grade-content">
        <main class="grade-container">
            
            <div class="student-info-card">
                <div class="student-name-id">
                    CAPILI, JUSTINE JAMES RAZO (2023-00075-CM-0)
                </div>
                
                <div class="academic-details">
                    <div class="detail-row">
                        <span class="badge">School Year <?php echo $academicYear; ?> <?php echo $semester; ?></span>
                    </div>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Admission Status</label>
                            <span>Continuing</span>
                        </div>
                        <div class="info-item">
                            <label>Scholastic Status</label>
                            <span>Regular</span>
                        </div>
                        <div class="info-item">
                            <label>Course Code & Description</label>
                            <span>BSIT-CM BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY (QUEZON CITY CAMPUS)</span>
                        </div>
                    </div>

                    <div class="gpa-section">
                        <label>GPA (excludes NSTP and subjects with non-numeric ratings)</label>
                        <span class="gpa-value">Grades Not Complete</span>
                    </div>
                </div>
            </div>

            <div class="white-content-container">
                <div class="grade-header">
                    <span>Final Grades</span>
                    <button onclick="downloadGradePDF()" class="btn-download">
                        Download Grade 📥
                    </button>
                </div>
                
                <div class="grade-table-wrapper">
                    <table class="grade-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Subject Code</th>
                                <th>Description</th>
                                <th>Faculty Name</th>
                                <th>Unit</th>
                                <th>Final Grade</th>
                                <th>Grade Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            /* ---------------------------------------------------------
                               PARA KAY KEN (DATABASE LOGIC):
                               
                               1. I-fetch ang student grades base sa Session ID.
                               2. I-calculate ang GPA base sa unit * grade.
                               3. Gamitin ang 'while' loop para sa bawat subject.

                               Sample Loop:
                               while($row = $result->fetch_assoc()) {
                                   $counter++;
                                   echo "<tr>
                                            <td>$counter</td>
                                            <td>" . $row['code'] . "</td>
                                            <td>" . $row['desc'] . "</td>
                                            <td>" . $row['faculty'] . "</td>
                                            <td>" . $row['units'] . "</td>
                                            <td>" . ($row['grade'] ?? '---') . "</td>
                                            <td><span class='status-passed'>" . $row['status'] . "</span></td>
                                         </tr>";
                               }
                               --------------------------------------------------------- */
                            ?>

                            <tr>
                                <td>1</td>
                                <td>COMP 015</td>
                                <td>Fundamentals of Research</td>
                                <td>AVENA, LEANDRO IV BADAL</td>
                                <td>3.0</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>COMP 017</td>
                                <td>Multimedia</td>
                                <td>ESCOBER, AIN GEUEL ESPEJO</td>
                                <td>3.0</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>COMP 018</td>
                                <td>Database Administration</td>
                                <td>DOROMAL, CHERRY</td>
                                <td>3.0</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>ELEC IT-E1</td>
                                <td>IT Elective 1</td>
                                <td>MONZON, KEZAIAH E</td>
                                <td>3.0</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>GEED 006</td>
                                <td>Art Appreciation/Pagpapahalaga sa Sining</td>
                                <td>DOLOROSA, RODRIGO S.</td>
                                <td>3.0</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <?php include '../Footer_Dashboard/FooterDashboard.php'; ?>

    <script src="../StudentHeader/header.js"></script>
    
    <script src="grade.js"></script>
</body>
</html>