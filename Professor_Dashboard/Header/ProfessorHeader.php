<?php
/**
 * ProfessorHeader.php
 * Fixed paths and active state logic
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kunin ang current page name para sa active state ng nav links
$current_page = basename($_SERVER['PHP_SELF']);

// Database info - siguraduhin na ang $conn ay available bago i-include ito
$profID = $_SESSION['professorID'] ?? $_SESSION['user_id'] ?? ''; 
$professor_name = "Faculty";
$professor_full_name = "Faculty Member";

// Kung connected sa DB, kunin ang totoong pangalan
if ($profID && isset($conn)) {
    try {
        $stmt = $conn->prepare("SELECT fname, lname FROM dbo.Professor WHERE professorID = ?");
        $stmt->execute([$profID]);
        $profData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($profData) {
            $professor_name = $profData['lname']; 
            $professor_full_name = $profData['fname'] . " " . $profData['lname'];
        }
    } catch (Exception $e) {
        // Silent error
    }
}
?>

<link rel="stylesheet" href="/Professor_Dashboard/Header/ProfessorHeader.css">

<header class="main-header">
    <div class="header-left">
        <div class="logo-container">
            <img src="/image/logo.png" alt="ISCP Logo" class="nav-logo">
        </div>
        <div class="h-title">
            <span class="main-t">ISCP</span>
            <span class="sub-t">Faculty Portal</span>
        </div>
    </div>

    <div class="menu-toggle" id="mobile-menu">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
    </div>

    <nav class="header-nav" id="nav-menu">
        <ul>
            <li><a href="/Professor_Dashboard/ProfessorDashboard.php" class="<?php echo ($current_page == 'ProfessorDashboard.php') ? 'active' : ''; ?>">Dashboard</a></li>
            <li><a href="/Professor_Dashboard/ClasslistPage/Classlist.php" class="<?php echo ($current_page == 'Classlist.php') ? 'active' : ''; ?>">Class List</a></li>
            <li><a href="/Professor_Dashboard/InputGradesPage/InputGrades.php" class="<?php echo ($current_page == 'InputGrades.php') ? 'active' : ''; ?>">Input Grades</a></li>
            
            <li class="profile-nav-item">
                <div class="user-profile">
                    <button class="prof-btn" id="facultyProfileBtn">
                        <span class="prof-name">Prof. <?php echo htmlspecialchars($professor_name); ?> 👨‍🏫</span>
                        <span class="arrow-down">▼</span>
                    </button>
                    
                  <div id="facultyDropdown" class="dropdown-content">
                    <div class="dropdown-header">
                        <strong><?php echo htmlspecialchars($professor_full_name); ?></strong>
                        <span><?php echo htmlspecialchars($profID); ?></span>
                    </div>
                    <hr>
                    <a href="/Professor_Dashboard/ProfilePage/profile.php">👤 My Profile</a>
                    
                    <a href="/Professor_Dashboard/ProfilePage/changepass.php">🔑 Change Password</a>
                    
                    <hr>
                    <a href="/logout.php" class="logout-btn">Log out</a>
                </div>
                
            </li>
        </ul>
    </nav>
</header>

<script src="/Professor_Dashboard/Header/ProfessorHeader.js"></script>