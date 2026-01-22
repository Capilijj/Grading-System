<?php
/**
 * changepass.php
 * UI for Changing Password.
 * * --- INSTRUCTION PARA KAY KEN ---
 * 1. Pre, i-validate mo muna kung 'old_pass' ay tama sa DB.
 * 2. Siguraduhin na 'new_pass' at 'confirm_pass' ay MATCH bago i-save.
 */

$student_info = "CAPILI, JUSTINE JAMES RAZO (2023-00075-CM-0)";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - ISCP</title>
    
    <link rel="stylesheet" href="../Header/header.css">
    <link rel="stylesheet" href="changepass.css">
    <link rel="stylesheet" href="../Footer/FooterDashboard.css">
</head>
<body>

    <?php include '../Header/header.php'; ?>

    <main class="changepass-container">
        <div class="changepass-card">
            
            <div class="student-label">
                <?php echo $student_info; ?>
            </div>

            <hr class="divider">

            <form action="#" method="POST" class="pass-form">
                
                <div class="input-wrapper">
                    <input type="password" name="old_pass" placeholder="Old Password" required>
                    <span class="icon">🔑</span>
                </div>

                <div class="input-wrapper">
                    <input type="password" name="new_pass" placeholder="New Password" required>
                    <span class="icon">🔑</span>
                </div>

                <div class="input-wrapper">
                    <input type="password" name="confirm_pass" placeholder="Confirm Password" required>
                    <span class="icon">🔑</span>
                </div>

                <hr class="divider">

                <div class="form-footer">
                    <button type="submit" class="btn-change-pass">Change Password</button>
                </div>

            </form>
        </div>
    </main>

    <?php include '../Footer/FooterDashboard.php'; ?>

    <script src="../Header/header.js"></script>
</body>
</html>