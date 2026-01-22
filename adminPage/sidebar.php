<?php 
// Kunin ang pangalan ng kasalukuyang file para sa active state highlight
$current_page = basename($_SERVER['PHP_SELF']); 
?>

<div class="sidebar">
    <div class="sidebar-header">
        <div class="logo-container">
            <img src="../../image/logo.png" alt="ISCP Logo">
        </div>
        <h1>ISCP Staff/Admin</h1>
    </div>

    <nav class="sidebar-nav">
        <a href="../User_management/Usermanagement.php" 
           class="nav-item <?= ($current_page == 'Usermanagement.php') ? 'active' : '' ?>">
            <span class="icon">👥</span> User Management
        </a>

        <a href="../Student_Management/Studentmanagement.php" 
           class="nav-item <?= ($current_page == 'Studentmanagement.php') ? 'active' : '' ?>">
            <span class="icon">👨‍🎓</span> Student Management
        </a>

        <a href="../Professor_management/Professormanagement.php" 
           class="nav-item <?= ($current_page == 'Professormanagement.php') ? 'active' : '' ?>">
            <span class="icon">👨‍🏫</span> Professor Management
        </a>

        <a href="../academic_control/academicyear.php" 
           class="nav-item <?= ($current_page == 'academicyear.php') ? 'active' : '' ?>">
            <span class="icon">🎓</span> Academic Control
        </a>

        <a href="../graderecords/graderecords.php" 
           class="nav-item <?= ($current_page == 'graderecords.php') ? 'active' : '' ?>">
            <span class="icon">📝</span> Grade Records
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="../../Login_FacultyPage/loginFaculty.php" class="logout-btn">
            <span class="icon">📤</span> Logout
        </a>
    </div>
</div>