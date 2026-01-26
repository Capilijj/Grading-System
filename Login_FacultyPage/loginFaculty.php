<?php
session_start();

// Prevent caching for security
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once '../Database/database_Connection.php'; 

$error = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputID = trim($_POST['faculty_id']); 
    $password = $_POST['password'];

    try {
        /**
         * CALL STORED PROCEDURE
         * Tinitiyak na ang input ay NVARCHAR para iwas Conversion Error
         */
        $stmt = $conn->prepare("{CALL sp_AuthenticateFaculty(?)}");
        $stmt->bindParam(1, $inputID, PDO::PARAM_STR); 
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userData) {
            // I-verify ang hashed password laban sa kinuha sa DB
            if (password_verify($password, $userData['HashedPassword'])) {
                
                // Clear any old session data
                session_regenerate_id(true);
                
                if ($userData['UserSource'] === 'AdminTable') {
                    $_SESSION['adminID'] = $userData['UserID'];
                    $_SESSION['user_id'] = $userData['UserID'];  // Fallback
                    $_SESSION['role'] = $userData['role'];
                    $_SESSION['name'] = $userData['fName'] . " " . $userData['lName'];
                    $_SESSION['fullName'] = $userData['fName'] . " " . $userData['lName'];
                    
                    // Update Last Login gamit ang VARCHAR ID
                    $update = $conn->prepare("UPDATE Admin SET lastLogin = GETDATE() WHERE CAST(adminID AS NVARCHAR(50)) = ?");
                    $update->execute([$userData['UserID']]);
                    
                    header("Location: ../adminPage/User_management/Usermanagement.php");
                } else {
                    // CRITICAL FIX: Set ALL session variables for professors
                    $_SESSION['professorID'] = $userData['UserID'];
                    $_SESSION['user_id'] = $userData['UserID'];  // Fallback/alternative key
                    $_SESSION['role'] = 'Professor';
                    $_SESSION['name'] = $userData['fName'] . " " . $userData['lName'];
                    $_SESSION['fullName'] = $userData['fName'] . " " . $userData['lName'];
                    $_SESSION['email'] = $userData['email'] ?? '';
                    $_SESSION['login_time'] = time();

                    // Update Last Login gamit ang VARCHAR ID
                    $update = $conn->prepare("UPDATE Professor SET lastLogin = GETDATE() WHERE CAST(professorID AS NVARCHAR(50)) = ?");
                    $update->execute([$userData['UserID']]);

                    header("Location: ../Professor_Dashboard/ProfessorDashboard.php");
                }
                exit();
            }
        }

        $error = "Invalid credentials or inactive account.";

    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    }
}

// Auto-redirect kung may session na
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'Professor') {
        header("Location: ../Professor_Dashboard/ProfessorDashboard.php");
    } else {
        header("Location: ../adminPage/User_management/Usermanagement.php");
    }
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
                <p class="instruction-text">Sign in using Faculty ID, Staff, or Admin Username</p>

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