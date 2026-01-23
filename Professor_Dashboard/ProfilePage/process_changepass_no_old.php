<?php
/**
 * process_changepass_no_old.php
 * Logic para sa pag-update ng password gamit ang Stored Procedure.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Database Connection
// Siguraduhin na tama ang path papunta sa iyong connection file
$connectionFile = __DIR__ . '/../../Database/database_Connection.php';
if (file_exists($connectionFile)) {
    include $connectionFile;
} else {
    die("Error: Database connection file not found at " . $connectionFile);
}

// 2. Security Check (Dapat Professor lang ang maka-access)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Professor') {
    header("Location: /Login_FacultyPage/loginFaculty.php");
    exit();
}

// 3. Main Logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_pass = $_POST['new_pass'] ?? '';
    $confirm_pass = $_POST['confirm_pass'] ?? '';
    $profID = $_SESSION['professorID'] ?? $_SESSION['user_id'] ?? '';

    // Basic Validation
    if (empty($new_pass) || empty($confirm_pass)) {
        echo "<script>alert('Please fill in all fields.'); window.history.back();</script>";
        exit();
    }

    if ($new_pass !== $confirm_pass) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit();
    }

    try {
        // I-hash ang password para sa security (Bcrypt)
        $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);

        // 4. Tawagin ang Stored Procedure (EXEC)
        $sql = "EXEC sp_UpdateProfessorPassword ?, ?";
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([$hashed_password, $profID]);

        if ($result) {
            echo "<script>
                    alert('Password updated successfully!');
                    window.location.href = 'profile.php';
                  </script>";
        } else {
            echo "<script>alert('Failed to update password.'); window.history.back();</script>";
        }

    } catch (Exception $e) {
        // I-log ang error pero huwag ipakita ang sensitive info sa user
        error_log($e->getMessage());
        echo "<script>alert('A database error occurred. Please try again later.'); window.history.back();</script>";
    }
} else {
    // Kapag sinubukang i-access ang file nang hindi nag-submit ng form
    header("Location: changepass.php");
    exit();
}
?>