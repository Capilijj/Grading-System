document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('mobile-menu');
    const navMenu = document.getElementById('nav-menu');
    const profileBtn = document.getElementById('profileBtn');
    const dropdown = document.getElementById('profileDropdown');

    // Hamburger Toggle
    if(menuToggle) {
        menuToggle.addEventListener('click', function() {
            this.classList.toggle('is-active');
            navMenu.classList.toggle('active');
        });
    }

    // Profile Toggle sa loob ng menu
    if(profileBtn) {
        profileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('show');
        });
    }

    // Close when clicking outside
    window.addEventListener('click', function(event) {
        if (!event.target.closest('.main-header')) {
            if (navMenu.classList.contains('active')) {
                navMenu.classList.remove('active');
                menuToggle.classList.remove('is-active');
            }
        }
    });
});