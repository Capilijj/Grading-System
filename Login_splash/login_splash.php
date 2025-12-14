<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>First Screen - ISCP</title>

    <!-- CSS file for the login splash / selection screen layout -->
    <link rel="stylesheet" href="login_splash.css"> 
    <!-- CSS file for slideshow styling -->
    <link rel="stylesheet" href="../SlideShows/slideshow.css"> 
</head>
<body>

    <!-- Main wrapper container -->
    <div class="container">

        <!-- PHP: Includes the slideshow component -->
        <?php 
            require_once '../SlideShows/slideshow.php'; 
        ?>

        <!-- Right-side section for user selection -->
        <div class="form-section selection-screen">

            <!-- ISCP logo -->
            <div class="iscp-logo">
                <img src="../image/logo.png" alt="ISCP University Logo">
            </div>

            <!-- Welcome heading -->
            <h1 class="welcome-text">HI, ISCPanians!</h1>

            <!-- Instruction message -->
            <p class="description-text">↓ Tap on your destination</p>

            <!-- Buttons for selecting user type -->
            <div class="button-group">
                <!-- Link to Student Login page -->
                <a href="../Login_StudentPage/loginStudent.php" 
                   class="action-button student-button">
                   STUDENT
                </a>

                <!-- Link to Faculty Login page -->
                <a href="../Login_FacultyPage/loginFaculty.php" 
                   class="action-button faculty-button">
                   FACULTY
                </a>
            </div>

            <!-- Terms and Privacy notice -->
            <p class="terms-text">
                By using this service, you have read, agreed, and acknowledged the
                <!-- Terms and Conditions link -->
                <a href="../Terms_of_Privacy/Terms_Conditions.php">
                    ISCP Terms and Conditions
                </a> and
                <!-- Privacy Policy link -->
                <a href="../Terms_of_Privacy/Privacy_Policy.php">
                    Privacy Policy
                </a>.
            </p>
        </div> 
    </div>     

    <!-- JavaScript file for slideshow functionality -->
    <script src="../SlideShows/slideshow.js"></script>
</body>
</html>
