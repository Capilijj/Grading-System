<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Professor') {
    header("Location: /Login_FacultyPage/loginFaculty.php");
    exit();
}

$professor_name = $_SESSION['name'] ?? "Faculty";
$professor_id = $_SESSION['professorID'] ?? "PROF-0000";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - ISCP Faculty</title>
    
    <link rel="stylesheet" href="/Professor_Dashboard/Header/ProfessorHeader.css">
    <link rel="stylesheet" href="/Professor_Dashboard/ProfilePage/changepass.css">
    <link rel="stylesheet" href="/User_Dashboard/Footer/FooterDashboard.css">
</head>
<body>

    <?php include '../Header/ProfessorHeader.php'; ?>

    <main class="changepass-container">
        <div class="changepass-card">
            
            <div class="student-label">
                <?php echo htmlspecialchars($professor_name); ?> (<?php echo htmlspecialchars($professor_id); ?>)
            </div>

            <hr class="divider">

            <form action="process_changepass_no_old.php" method="POST" class="pass-form">
                
                <div class="input-wrapper">
                    <input type="password" name="new_pass" placeholder="Enter New Password" required>
                    <span class="icon">🔑</span>
                </div>

                <div class="input-wrapper">
                    <input type="password" name="confirm_pass" placeholder="Confirm New Password" required>
                    <span class="icon">🔑</span>
                </div>

                <hr class="divider">

                <div class="form-footer">
                    <button type="submit" class="btn-change-pass">Update Password</button>
                </div>

            </form>
        </div>
    </main>

    <?php include '../../User_Dashboard/Footer/FooterDashboard.php'; ?>

    <script src="/Professor_Dashboard/Header/ProfessorHeader.js"></script>
</body>
</html>