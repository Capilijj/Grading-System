<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Login - ISCP</title>
    
    <!-- CSS file for Student Login page styling -->
    <link rel="stylesheet" href="loginStudent.css">
    
    <!-- CSS file for slideshow styling -->
    <link rel="stylesheet" href="../SlideShows/slideshow.css"> 
</head>
<body>

    <!-- Main container that holds the slideshow and login form -->
    <div class="container">

        <!-- PHP: Includes the slideshow component -->
        <?php 
            require_once '../SlideShows/slideshow.php'; 
        ?>

        <!-- Right section: Student login form -->
        <div class="form-section">

            <!-- Header section containing logo and text -->
            <div class="header-content">

                <!-- Circular logo container -->
                <div class="logo-circle">
                    <img src="../../image/logo.png" alt="ISCP Logo">
                </div>

                <!-- Page title -->
                <h2 class="title-text">ISCP-Student</h2>

                <!-- Instruction text -->
                <p class="instruction-text">Sign in to start your session</p>
            </div>

            <!-- Login form -->
            <!-- Sends input data to process_student_login.php using POST method -->
            <form action="../User_Dashboard/StudentDashboard.php" method="POST" class="login-form">

                <!-- Student number input -->
                <div class="input-group">
                    <input type="text" id="student-number" 
                           name="student_number" 
                           placeholder="Student number" required>
                </div>

                <!-- Password input -->
                <div class="input-group">
                    <input type="password" id="password" 
                           name="password" 
                           placeholder="Password" required>
                </div>
                
                <!-- Forgot password link -->
                <div class="forgot-links">
                    <a href="#" class="forgot-password-link">Forgot Password?</a>
                </div>
                
                <!-- Submit button -->
                <button type="submit" class="sign-in-button">
                    Sign in
                </button>
            </form>

            <!-- Footer link to return to selection screen -->
            <div class="Back-links">
                <a href="../Login_splash/login_splash.php" class="back-link">
                    ← Back to selection screen
                </a>
            </div>
            
        </div>
    </div>     

    <!-- JavaScript file for Student Login interactions -->
    <script src="loginStudent.js"></script>

    <!-- JavaScript file for slideshow functionality -->
    <script src="../SlideShows/slideshow.js"></script>
</body>
</html>
