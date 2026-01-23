<?php
session_start();

// 1. I-check ang role bago i-clear ang session
$redirect_url = "../../Login_FacultyPage/loginFaculty.php"; // Default redirect

if (isset($_SESSION['role']) && $_SESSION['role'] === 'Student') {
    $redirect_url = "../../Login_StudentPage/loginStudent.php";
}

// 2. Alisin ang lahat ng session variables
$_SESSION = array();

// 3. Burahin ang session cookie sa browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Sirain ang session sa server
session_destroy();

// 5. I-redirect sa tamang login page base sa role kanina
header("Location: " . $redirect_url);
exit();
?>