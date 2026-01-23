<?php
session_start();

// 1. Database Connection
include '../Database/database_Connection.php'; 

$error = ""; // Placeholder para sa error message

// 2. PHP Logic para sa Validation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_no = trim($_POST['student_number']);
    $password = trim($_POST['password']);

    if (empty($student_no) || empty($password)) {
        $error = "Please enter both student number and password.";
    } else {
        try {
            // Tawagin ang Stored Procedure
            $stmt = $conn->prepare("EXEC sp_LoginStudent ?");
            $stmt->execute([$student_no]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Password check (Ginamit ang password_verify)
                if (password_verify($password, $user['password'])) {
                    
                    $_SESSION['role'] = 'Student';
                    $_SESSION['studentID'] = $user['studentID'];
                    $_SESSION['name'] = $user['fname'] . " " . $user['lname'];
                    $_SESSION['courseID'] = $user['courseID'];

                    header("Location: ../User_Dashboard/StudentDashboard.php");
                    exit();
                } else {
                    $error = "Incorrect password. Please try again.";
                }
            } else {
                $error = "Student number not found.";
            }
        } catch (Exception $e) {
            $error = "System error. Please contact admin.";
        }
    }
}

// Redirect kung session is active
if (isset($_SESSION['role']) && $_SESSION['role'] === 'Student') {
    header("Location: ../User_Dashboard/StudentDashboard.php");
    exit();
}

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - ISCP</title>
    <link rel="stylesheet" href="../Login_StudentPage/loginStudent.css">
    <link rel="stylesheet" href="../Login_splash/SlideShows/slideshow.css"> 
</head>
<body>

    <div class="container">
        <?php require_once '../Login_splash/SlideShows/slideshow.php'; ?>

        <div class="form-section">
            <div class="header-content">
                <div class="logo-circle">
                    <img src="../../image/logo.png" alt="ISCP Logo">
                </div>
                <h2 class="title-text">ISCP-Student</h2>
                <p class="instruction-text">Sign in to start your session</p>

                <?php if(!empty($error)): ?>
                    <div class="error-text-msg" style="color: #ffda00; background: rgba(255,0,0,0.2); padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 0.9rem; font-weight: bold; border: 1px solid red;">
                        ⚠️ <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
            </div>

            <form action="" method="POST" class="login-form">
                <div class="input-group">
                    <input type="text" name="student_number" placeholder="Student number" 
                           value="<?php echo isset($_POST['student_number']) ? htmlspecialchars($_POST['student_number']) : ''; ?>" required>
                </div>

                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                
                <button type="submit" class="sign-in-button">Sign in</button>
            </form>

            <div class="Back-links">
                <a href="../Login_splash/login_splash.php" class="back-link">
                    ← Back to selection screen
                </a>
            </div>
        </div>
    </div>     

    <script src="../Login_splash/loginStudent.js"></script>
    <script src="../Login_splash/SlideShows/slideshow.js"></script>
</body>
</html>