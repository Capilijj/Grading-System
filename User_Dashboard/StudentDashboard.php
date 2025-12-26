<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - ISCP</title>
    
    <link rel="stylesheet" href="../Header_Dashboard/header.css">
    <link rel="stylesheet" href="StudentDashboard.css">
    <link rel="stylesheet" href="../Footer_Dashboard/FooterDashboard.css">
</head>
<body>

    <?php include '../Header_Dashboard/header.php'; ?>

    <main class="dashboard-container">
        
        <section class="hero-section">
            <div class="hero-overlay">
                <div class="hero-content">
                    <h2>EMPOWERING SCHOOLS</h2>
                    <h3>WITH FAST AND ACCURATE GRADE</h3>
                    <h4>MANAGEMENT.</h4>
                    <p>Innovative Education, One Click at a Time.</p>
                </div>
            </div>
        </section>

        <div class="white-content-container floating-top">
            <div class="user-info-bar">
                CAPILI, JUSTINE JAMES RAZO (2023-00075-CM-0)
            </div>

            <section class="stats-grid">
                <div class="card orange-card">
                    <p>Current GPA</p>
                    <div class="card-value">1.67</div>
                </div>
                <div class="card teal-card">
                    <p>Total Units Enrolled</p>
                    <div class="card-value">8</div>
                </div>
                <div class="card teal-card">
                    <p>Incomplete Grades</p>
                    <div class="card-value">0</div>
                </div>
                <div class="card teal-card">
                    <p>Account Balance</p>
                    <div class="card-value small-text">Free-Educ</div>
                </div>
            </section>
        </div>

        <section class="white-content-container">
            <div class="guidelines-header">
                ISCP ACADEMIC COMPLIANCE GUIDELINES (ACG)
            </div>
            
            <div class="guidelines-content">
                <p class="intro-text">These are the guidelines for the ISCP (Institute of Scholars and Certified Professionals) governing the maintenance of eligibility and benefits under the Free Education Law (e.g., RA 10931).</p>
                
                <div class="rule-block">
                    <h4>I. ELIGIBILITY AND QUALIFICATION</h4>
                    <p>All students who have satisfied the admission requirements of ISCP and do not fall into the "ineligible" categories stated in the Free Higher Education (FHE) Act are entitled to avail of Free Education.</p>
                </div>

                <div class="rule-block">
                    <h4>2. SCHOLASTIC DELINQUENCY MATRIX</h4>
                    <p class="sub-text">To continuously enjoy the Free Education benefits, a student must maintain a Good Scholastic Standing.</p>
                    
                    <p class="table-title">FOR STUDENTS WITH A LOAD OF 22 UNITS AND ABOVE IN THE PREVIOUS SEMESTER</p>
                    
                    <table class="delinquency-table">
                        <thead>
                            <tr>
                                <th>Number of Subject Failed/ Withdrawn/ Dropped</th>
                                <th>Action to be Taken</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1 subject</td><td>Verbal Warning</td><td>Requires mandatory consultation with the Program Coordinator.</td>
                            </tr>
                            <tr>
                                <td>2 subjects</td><td>Written Warning (Reduced load by 3 units)</td><td>Scholarship remains, but the student must pass all subjects in the current semester.</td>
                            </tr>
                            <tr>
                                <td>3 subjects</td><td>Probation (Reduced load by 6 units)</td><td>Scholarship will be forfeited for the following semester.</td>
                            </tr>
                            <tr>
                                <td>4 subjects or more</td><td>Dismissal</td><td>Deemed Dropped from the University.</td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="note">Note: A student who acquires a "Written Warning" for two (2) non-consecutive semesters, the Free Education Scholarship Grant will be automatically forfeited.</p>
                </div>

                <div class="rule-block">
                    <h4>3. MAXIMUM RESIDENCY AND YEARS OF STAY</h4>
                    <p>If a student stays beyond the allowed number of years covered by the Free Education Law for their course, they shall pay the tuition and miscellaneous fees for the remaining allowable years of stay, provided this is still within the maximum residency period of ISCP.</p>
                </div>

                <div class="rule-block">
                    <h4>4. FILING A LEAVE OF ABSENCE (LOA)</h4>
                    <p>The procedure for filing an LOA shall follow the existing ISCP Student Handbook. With permission from the University, the LOA will enable the student to still enjoy the scholarship upon their return, and the LOA period will not be counted against the maximum covered years.</p>
                </div>

                <div class="rule-block">
                    <h4>5. ADHERENCE TO EXISTING POLICIES</h4>
                    <p>All existing policies of ISCP (Admission, Retention, and Graduation rules) shall be upheld and used to complement the implementation of the Free Education Act.</p>
                </div>

                <div class="rule-block">
                    <h4>6. POLICY PRIORITY</h4>
                    <p>For provisions inconsistent with the ISCP Student Handbook, the stipulations in these ISCP Academic Compliance Guidelines (ACG) shall prevail.</p>
                </div>
            </div>

        </section>
    </main>

    <?php include '../Footer_Dashboard/FooterDashboard.php'; ?>
    <script src="../Header_Dashboard/header.js"></script>
</body>
</html>