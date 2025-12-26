// header.js

document.addEventListener('DOMContentLoaded', function() {
    const profileBtn = document.getElementById('profileBtn');
    const dropdown = document.getElementById('profileDropdown');

    // Kapag kinlik ang profile button
    profileBtn.addEventListener('click', function(e) {
        e.stopPropagation(); // Pinipigilan nito ang pag-close agad
        dropdown.classList.toggle('show');
    });

    // Isara ang dropdown kapag nag-click sa labas
    window.addEventListener('click', function(event) {
        if (!event.target.closest('.user-profile')) {
            if (dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            }
        }
    });
});