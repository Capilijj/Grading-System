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
       
        <a href="../scheduling/manage_schedule.php" 
            class="nav-item <?= ($current_page == 'manage_schedule.php') ? 'active' : '' ?>">
                <span class="icon">📅</span> Class Scheduling
        </a>
        
    </nav>

    <div class="sidebar-footer">
        <a href="../../logout.php" class="logout-btn">
            <span class="icon">📤</span> Logout
        </a>
    </div>
</div>