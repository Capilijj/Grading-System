document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('mobile-menu');
    const navMenu = document.getElementById('nav-menu');
    const profileBtn = document.getElementById('facultyProfileBtn');
    const dropdown = document.getElementById('facultyDropdown');

    // Hamburger Menu Toggle
    if(menuToggle && navMenu) {
        menuToggle.addEventListener('click', function() {
            this.classList.toggle('is-active');
            navMenu.classList.toggle('active');
        });
    }

    // Profile Dropdown Toggle
    if(profileBtn && dropdown) {
        profileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('show');
        });
    }

    // Isara ang dropdown kapag nag-click sa labas
    window.addEventListener('click', function(event) {
        if (dropdown && dropdown.classList.contains('show')) {
            if (!event.target.closest('.user-profile')) {
                dropdown.classList.remove('show');
            }
        }
        
        if (navMenu && navMenu.classList.contains('active')) {
            if (!event.target.closest('.main-header')) {
                navMenu.classList.remove('active');
                if(menuToggle) menuToggle.classList.remove('is-active');
            }
        }
    });
});