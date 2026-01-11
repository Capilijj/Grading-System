document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('mobile-menu');
    const navMenu = document.getElementById('nav-menu');
    const profileBtn = document.getElementById('facultyProfileBtn');
    const dropdown = document.getElementById('facultyDropdown');

    // Hamburger Menu Toggle
    if(menuToggle) {
        menuToggle.addEventListener('click', function(e) {
            e.stopPropagation(); // Pinipigilan ang pag-close agad
            this.classList.toggle('is-active');
            navMenu.classList.toggle('active');
        });
    }

    // Profile Dropdown Toggle
    if(profileBtn) {
        profileBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // Pinipigilan ang pag-close agad
            dropdown.classList.toggle('show');
        });
    }

    // Isara ang menu o dropdown kapag nag-click sa labas
    window.addEventListener('click', function(event) {
        // Isara ang Mobile Menu kung naka-open at nag-click sa labas
        if (navMenu && navMenu.classList.contains('active')) {
            if (!navMenu.contains(event.target) && !menuToggle.contains(event.target)) {
                navMenu.classList.remove('active');
                menuToggle.classList.remove('is-active');
            }
        }
        
        // Isara ang Dropdown kung naka-show at nag-click sa labas
        if (dropdown && dropdown.classList.contains('show')) {
            if (!profileBtn.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        }
    });
});