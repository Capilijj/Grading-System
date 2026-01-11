<?php
/**
 * REKTA REDIRECTION LOGIC
 * Kapag pinindot ang Sign In, titingnan kung may pinadalang data (POST).
 * Dahil placeholder lang, rekta agad sa Dashboard.
 */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // KEN: Dito mo isasaksak yung database query mo balang araw bago ang header redirect.
    header("Location: ../Professor_Dashboard/ProfessorDashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Login - ISCP</title>
    
    <link rel="stylesheet" href="loginFaculty.css">
    <link rel="stylesheet" href="../SlideShows/slideshow.css"> 
</head>
<body>

    <div class="container">

        <?php 
            require_once '../SlideShows/slideshow.php'; 
        ?>
            
        <div class="form-section">

            <div class="header-content">
                <div class="logo-circle faculty-logo-color">
                    <img src="../../image/logo.png" alt="ISCP Logo">
                </div>

                <h2 class="title-text">ISCP-Faculty</h2>
                <p class="instruction-text">Sign in to start your session</p>
            </div>

            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="login-form">
                
                <div class="input-group">
                    <input type="text" id="faculty-id" name="faculty_id" 
                           placeholder="Faculty ID" required>
                </div>
                
                <div class="input-group">
                    <input type="password" id="password" name="password" 
                           placeholder="Password" required>
                </div>
                
                <div class="forgot-links">
                    <a href="forgotpass.php" class="forgot-password-link">Forgot Password?</a>
                </div>

                <button type="submit" class="sign-in-button faculty-button-style">
                    Sign in
                </button>
            </form>

            <div class="Back-links">
                <a href="../Login_splash/login_splash.php" class="back-link">
                    ← Back to selection screen
                </a>
            </div>
            
        </div>
    </div>     

    <script src="../SlideShows/slideshow.js"></script>
    <script src="loginFaculty.js"></script>
</body>
</html>