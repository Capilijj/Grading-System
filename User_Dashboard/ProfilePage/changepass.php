<?php
session_start();
require_once __DIR__ . '/../../Database/database_Connection.php';

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Check if student is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Student') {
    header("Location: ../../Login_StudentPage/loginStudent.php");
    exit();
}

$studentID = $_SESSION['studentID'] ?? '';
$success_msg = "";
$error_msg = "";

// --- PASSWORD UPDATE LOGIC ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_pass'])) {
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];

    if ($new_pass === $confirm_pass) {
        try {
            // Tinatawag ang Stored Procedure
            $stmt = $conn->prepare("{call sp_UpdateStudentPassword(?, ?)}");
            $stmt->execute([$studentID, $new_pass]);
            $success_msg = "Password updated successfully!";
        } catch (Exception $e) {
            $error_msg = "Error: " . $e->getMessage();
        }
    } else {
        $error_msg = "Passwords do not match!";
    }
}

// Fetch name for display
try {
    $stmt_name = $conn->prepare("SELECT fName, lName FROM dbo.Student WHERE studentID = ?");
    $stmt_name->execute([$studentID]);
    $user = $stmt_name->fetch(PDO::FETCH_ASSOC);
    $fullName = strtoupper(($user['lName'] ?? '') . ", " . ($user['fName'] ?? ''));
} catch (Exception $e) { $fullName = "STUDENT"; }
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
                <?php echo htmlspecialchars($fullName); ?> (<?php echo htmlspecialchars($studentID); ?>)
            </div>

            <hr class="divider">

            <?php if($success_msg) echo "<p style='color:green; font-size:0.8rem;'>$success_msg</p>"; ?>
            <?php if($error_msg) echo "<p style='color:red; font-size:0.8rem;'>$error_msg</p>"; ?>

            <form action="" method="POST" class="pass-form">
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
                    <button type="submit" name="change_pass" class="btn-change-pass">Update Password</button>
                </div>
            </form>
        </div>
    </main>

    <?php include '../Footer/FooterDashboard.php'; ?>

    <script src="../Header/header.js"></script>
</body>
</html>