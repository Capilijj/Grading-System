<?php
session_start();

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once '../Database/database_Connection.php'; 

$error = ""; // Placeholder para sa text validation

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputID = trim($_POST['faculty_id']); 
    $password = $_POST['password'];

    try {
        /**
         * LOGIC 1: ADMIN (PLAIN TEXT)
         */
        $adminStmt = $conn->prepare("SELECT adminID, fName, lName, role FROM Admin 
                                   WHERE (CAST(username AS NVARCHAR(50)) = ? OR CAST(adminID AS NVARCHAR(50)) = ?) 
                                   AND password = ? AND status = 'Active'");
        $adminStmt->execute([$inputID, $inputID, $password]);
        $adminUser = $adminStmt->fetch(PDO::FETCH_ASSOC);

        if ($adminUser) {
            $_SESSION['adminID'] = $adminUser['adminID'];
            $_SESSION['role'] = $adminUser['role'];
            $_SESSION['name'] = $adminUser['fName'] . " " . $adminUser['lName'];
            
            $conn->prepare("UPDATE Admin SET lastLogin = GETDATE() WHERE adminID = ?")
                 ->execute([$adminUser['adminID']]);
            
            header("Location: ../adminPage/User_management/Usermanagement.php");
            exit();
        }

        /**
         * LOGIC 2: PROFESSOR (HASHED)
         */
        $profStmt = $conn->prepare("SELECT professorID, fName, lName, password, employmentStatus 
                                   FROM Professor 
                                   WHERE CAST(professorID AS NVARCHAR(50)) = ? 
                                   AND employmentStatus LIKE 'Active%'"); 
        $profStmt->execute([$inputID]);
        $profUser = $profStmt->fetch(PDO::FETCH_ASSOC);

        if ($profUser && password_verify($password, $profUser['password'])) {
            $_SESSION['professorID'] = $profUser['professorID'];
            $_SESSION['role'] = 'Professor';
            $_SESSION['name'] = $profUser['fName'] . " " . $profUser['lName'];

            $conn->prepare("UPDATE Professor SET lastLogin = GETDATE() WHERE professorID = ?")
                 ->execute([$profUser['professorID']]);

            header("Location: ../Professor_Dashboard/ProfessorDashboard.php");
            exit();
        }

        // TEXT VALIDATION ERROR
        $error = "Invalid credentials or inactive account.";

    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    }
}

// Redirect kung naka-login na
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'Professor') header("Location: ../Professor_Dashboard/ProfessorDashboard.php");
    else header("Location: ../adminPage/User_management/Usermanagement.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty & Admin Login - ISCP</title>
    <link rel="stylesheet" href="../Login_StudentPage/loginStudent.css">
    <link rel="stylesheet" href="loginFaculty.css">
    <link rel="stylesheet" href="../Login_splash/SlideShows/slideshow.css"> 
</head>
<body>
    <div class="container">
        <?php require_once '../Login_splash/SlideShows/slideshow.php'; ?>
            
        <div class="form-section faculty-theme">
            <div class="header-content">
                <div class="logo-circle faculty-logo-border">
                    <img src="../../image/logo.png" alt="ISCP Logo">
                </div>
                <h2 class="title-text">ISCP Portal</h2>
                <p class="instruction-text">Sign in using Faculty ID or Admin Username</p>

                <?php if(!empty($error)): ?>
                    <div class="error-text-msg">
                        ⚠️ <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
            </div>

            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="login-form">
                <div class="input-group">
                    <input type="text" name="faculty_id" placeholder="ID or Username" 
                           value="<?php echo isset($_POST['faculty_id']) ? htmlspecialchars($_POST['faculty_id']) : ''; ?>" required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="sign-in-button faculty-btn">Sign in</button>
            </form>

            <div class="Back-links">
                <a href="../Login_splash/login_splash.php" class="back-link">← Back to selection screen</a>
            </div>
        </div>
    </div>     
    <script src="../Login_splash/SlideShows/slideshow.js"></script>
</body>
</html>