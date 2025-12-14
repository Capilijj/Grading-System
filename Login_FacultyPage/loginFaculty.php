<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Character encoding -->
    <meta charset="UTF-8">

    <!-- Makes the page responsive on mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Page title -->
    <title>Faculty Login - ISCP</title>
    
    <!-- CSS file for Faculty Login page design -->
    <link rel="stylesheet" href="loginFaculty.css">

    <!-- CSS file for slideshow styling -->
    <link rel="stylesheet" href="../SlideShows/slideshow.css"> 
</head>
<body>

    <!-- Main container that holds slideshow and login form -->
    <div class="container">

        <!-- PHP: Includes the slideshow content from another file -->
        <?php 
            require_once '../SlideShows/slideshow.php'; 
        ?>
            
        <!-- Right section: Faculty login form -->
        <div class="form-section">

            <!-- Header section: logo, title, and instructions -->
            <div class="header-content">
                <!-- Circular logo container -->
                <div class="logo-circle faculty-logo-color">
                    <img src="../../image/logo.png" alt="ISCP Logo">
                </div>

                <!-- Page title -->
                <h2 class="title-text">ISCP-Faculty</h2>

                <!-- Instruction text -->
                <p class="instruction-text">Sign in to start your session</p>
            </div>

            <!-- Login form -->
            <!-- Sends data to process_faculty_login.php using POST method -->
            <form action="process_faculty_login.php" method="POST" class="login-form">
                
                <!-- Faculty ID input field -->
                <div class="input-group">
                    <input type="text" id="faculty-id" name="faculty_id" 
                           placeholder="Faculty ID" required>
                </div>
                
                <!-- Password input field -->
                <div class="input-group">
                    <input type="password" id="password" name="password" 
                           placeholder="Password" required>
                </div>
                
                <!-- Forgot password link -->
                <div class="forgot-links">
                    <a href="#" class="forgot-password-link">Forgot Password?</a>
                </div>

                <!-- Submit button -->
                <button type="submit" class="sign-in-button faculty-button-style">
                    Sign in
                </button>
            </form>

            <!-- Link to go back to login selection screen -->
            <div class="Back-links">
                <a href="../Login_splash/login_splash.php" class="back-link">
                    ← Back to selection screen
                </a>
            </div>
            
        </div>
    </div>     

    <!-- JavaScript file for slideshow functionality -->
    <script src="../SlideShows/slideshow.js"></script>

    <!-- JavaScript file for faculty login interactions -->
    <script src="loginFaculty.js"></script>
</body>
</html>
