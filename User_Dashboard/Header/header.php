<?php
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = dirname($_SERVER['PHP_SELF']);

// Determine the base path relative to User_Dashboard
if (strpos($current_dir, '/User_Dashboard') === 0 && $current_dir !== '/User_Dashboard') {
    $base_path = '../';
} else {
    $base_path = '';
}
?>

<link rel="stylesheet" href="header.css">

<header class="main-header">
    <div class="header-left">
        <div class="logo-container">
            <img src="../../image/logo.png" alt="ISCP Logo" class="nav-logo">
        </div>
        <h1 class="brand-name">ISCP</h1>
    </div>

    <div class="menu-toggle" id="mobile-menu">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
    </div>

    <nav class="header-nav" id="nav-menu">
        <ul>
            <li>
                <a href="<?php echo $base_path; ?>StudentDashboard.php" class="<?php echo ($current_page == 'StudentDashboard.php') ? 'active' : ''; ?>">Home</a>
            </li>
            <li>
                <a href="<?php echo $base_path; ?>SchedulePage/schedule.php" class="<?php echo ($current_page == 'schedule.php') ? 'active' : ''; ?>">Schedule</a>
            </li>
            <li>
                <a href="<?php echo $base_path; ?>GradePage/grade.php" class="<?php echo ($current_page == 'grade.php') ? 'active' : ''; ?>">Grades</a>
            </li>
            
            <li class="profile-nav-item">
                <div class="user-profile">
                    <button class="profile-trigger" id="profileBtn">
                        <span class="profile-emoji">👤</span>
                    </button>
                    <div id="profileDropdown" class="dropdown-content">
                        <a href="../../User_Dashboard/ProfilePage/profile.php">My Profile</a>
                        <a href="../../User_Dashboard/ProfilePage/changepass.php">Change Pass</a>
                        <hr style="border: 0; border-top: 1px solid #eee; margin: 0;">
                        <a href="../../Login_StudentPage/loginStudent.php" class="logout-btn">Log out</a>
                    </div>
                </div>
            </li>
        </ul>
    </nav>
</header>

<script src="header.js"></script>