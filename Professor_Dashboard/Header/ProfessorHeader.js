document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('mobile-menu');
    const navMenu = document.getElementById('nav-menu');
    const profileBtn = document.getElementById('facultyProfileBtn');
    const dropdown = document.getElementById('facultyDropdown');

    // Hamburger Menu Toggle
    if(menuToggle) {
        menuToggle.addEventListener('click', function() {
            this.classList.toggle('is-active');
            navMenu.classList.toggle('active');
        });
    }

    // Profile Dropdown Toggle
    if(profileBtn) {
        profileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('show');
        });
    }

    // Isara ang menu o dropdown kapag nag-click sa labas
    window.addEventListener('click', function(event) {
        if (!event.target.closest('.main-header')) {
            if (navMenu.classList.contains('active')) {
                navMenu.classList.remove('active');
                menuToggle.classList.remove('is-active');
            }
        }
        if (!event.target.closest('.user-profile')) {
            dropdown.classList.remove('show');
        }
    });
});