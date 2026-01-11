<?php
/**
 * forgotpass.php
 * UI for Forgot Password.
 * * --- INSTRUCTION PARA KAY KEN ---
 * 1. Dito mo i-checheck kung ang Email o Student ID ay exist sa database.
 * 2. Kapag exist, padalhan siya ng Reset Link sa email o ituloy sa Security Question.
 * 3. kht hindi mo na ito ma  achieve since ui lang naman need dito no need to demo
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - ISCP</title>
    
    <link rel="stylesheet" href="forgotpass.css">
</head>
<body>

    <main class="forgot-container">
        <div class="forgot-card">
            
            <div class="header-section">
                <div class="logo-circle">
                    <img src="../../image/logo.png" alt="ISCP Logo">
                </div>
                <h2>Forgot Password?</h2>
                <p>Enter your student email to reset your password.</p>
            </div>

            <hr class="divider">

            <form action="#" method="POST" class="forgot-form">
                
                <div class="input-wrapper">
                    <label>Student Email Address</label>
                    <input type="email" name="email" placeholder="e.g. j..2023@iscp.edu.ph" required>
                    <span class="icon">📧</span>
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn-reset">Send Reset Link</button>
                    <a href="../Login_StudentPage/loginStudent.php" class="back-link">Back to Login</a>
                </div>

            </form>
        </div>
    </main>

</body>
</html>