<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="../ProfessorHeader/ProfessorHeader.css">

<header class="main-header">
    <div class="header-left">
        <div class="logo-container">
            <img src="../image/logo.png" alt="ISCP Logo" class="nav-logo">
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
            <li><a href="../Professor_Dashboard/ProfessorDashboard.php" class="<?php echo ($current_page == 'ProfessorDashboard.php') ? 'active' : ''; ?>">Dashboard</a></li>
            <li><a href="../ClasslistPage/Classlist.php" class="<?php echo ($current_page == 'Classlist.php') ? 'active' : ''; ?>">Class List</a></li>
            <li><a href="../InputGradesPage/InputGrades.php" class="<?php echo ($current_page == 'InputGrades.php') ? 'active' : ''; ?>">Input Grades</a></li>
            
            <li class="profile-nav-item">
                <div class="user-profile">
                    <button class="prof-btn" id="facultyProfileBtn">
                        <span class="prof-name">Prof. Razo 👨‍🏫</span>
                        <span class="arrow-down">▼</span>
                    </button>
                    
                    <div id="facultyDropdown" class="dropdown-content">
                        <div class="dropdown-header">
                            <strong>Justine James Razo</strong>
                            <span>Faculty Member</span>
                        </div>
                        <hr>
                        <a href="../ProfilePage/profile.php">👤 My Profile</a>
                        <a href="../ProfilePage/changepass.php">🔑 Change Password</a>
                        <hr>
                        <a href="../Login_splash/login_splash.php" class="logout-btn">Log out</a>
                    </div>
                </div>
            </li>
        </ul>
    </nav>
</header>

<script src="../ProfessorHeader/ProfessorHeader.js"></script>