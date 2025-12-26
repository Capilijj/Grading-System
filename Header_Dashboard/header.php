<link rel="stylesheet" href="header.css">

<header class="main-header">
    <div class="header-left">
        <div class="logo-container">
            <img src="../../image/logo.png" alt="ISCP Logo" class="nav-logo">
        </div>
        <h1 class="brand-name">ISCP</h1>
    </div>

    <nav class="header-nav">
        <ul>
            <li><a href="home.php" class="active">Home</a></li>
            <li><a href="Schedule.php">Schedule</a></li>
            <li><a href="grade.php">Grades</a></li>
       
        </ul>
    </nav>

    <div class="header-right">
        <div class="user-profile">
            <button class="profile-trigger" id="profileBtn">
                <i class="user-icon">👤</i>
            </button>
            <div id="profileDropdown" class="dropdown-content">
                <a href="profile.php">Profile</a>
                <a href="settings.php">Change Pass</a>
                <hr>
                <a href="../Login_StudentPage/loginStudent.php" class="logout-btn">Log out</a>
            </div>
        </div>
    </div>
</header>

<script src="header.js"></script>