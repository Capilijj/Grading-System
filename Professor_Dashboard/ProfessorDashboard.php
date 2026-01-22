<?php
/**
 * ProfessorDashboard.php
 * Updated: Instant PDF Download via html2pdf.js
 */
$faculty_name = "PROF. RAZO, JUSTINE JAMES"; 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professor Dashboard - ISCP</title>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <link rel="stylesheet" href="Header/ProfessorHeader.css">
    <link rel="stylesheet" href="ProfessorDashboard.css">
    <link rel="stylesheet" href="../User_Dashboard/Footer/FooterDashboard.css">
</head>
<body>

    <?php include 'Header/ProfessorHeader.php'; ?>

    <main class="faculty-main">
        <section class="faculty-hero">
            <div class="hero-text">
                <h1>HI!, <?php echo $faculty_name; ?>!</h1>
                <p>International State College of the Philippines | Academic Portal</p>
            </div>
        </section>

        <section class="metrics-grid">
            <div class="m-card">
                <div class="m-icon" style="background: #e67e22;">📚</div>
                <div class="m-data">
                    <span class="m-label">Subjects Handled</span>
                    <span class="m-number">05</span>
                </div>
            </div>
            <div class="m-card">
                <div class="m-icon" style="background: #16a085;">👨‍🎓</div>
                <div class="m-data">
                    <span class="m-label">Total Students</span>
                    <span class="m-number">184</span>
                </div>
            </div>
            <div class="m-card">
                <div class="m-icon" style="background: #2980b9;">🕒</div>
                <div class="m-data">
                    <span class="m-label">Classes Today</span>
                    <span class="m-number">03</span>
                </div>
            </div>
        </section>

        <div class="lower-grid full-width" id="schedule-content">
            <div class="card-container table-card">
                <div class="card-title">
                    <h3>Today's Class Schedule</h3>
                    <div class="card-actions">
                        <button class="btn-download" id="downloadSchedBtn">
                            <span>📥</span> Download PDF
                        </button>
                        <button class="btn-sm">View Full Schedule</button>
                    </div>
                </div>
                
                <div class="table-wrapper">
                    <table class="fac-table">
                        <thead>
                            <tr>
                                <th>Subject Code & Description</th>
                                <th>Section</th>
                                <th>Time</th>
                                <th>Room</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>CS202</strong> - Data Structures</td>
                                <td>BSCS 2-A</td>
                                <td>8:00 AM - 10:00 AM</td>
                                <td>CL-1</td>
                                <td><span class="status-tag">Upcoming</span></td>
                            </tr>
                            <tr>
                                <td><strong>IT101</strong> - Intro Computing</td>
                                <td>BSIT 1-C</td>
                                <td>10:30 AM - 12:30 PM</td>
                                <td>CL-2</td>
                                <td><span class="status-tag">Upcoming</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php include '../User_Dashboard/Footer/FooterDashboard.php'; ?>

    <script src="Header/ProfessorHeader.js"></script>
    <script src="ProfessorDashboard.js"></script>
</body>
</html>