document.addEventListener('DOMContentLoaded', function() {
    const profBtn = document.getElementById('facultyProfileBtn');
    const dropdown = document.getElementById('facultyDropdown');

    // 1. Dropdown Toggle Fix
    if (profBtn && dropdown) {
        profBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('show');
        });
    }

    // 2. Close dropdown when clicking outside
    window.addEventListener('click', function(e) {
        if (dropdown && dropdown.classList.contains('show')) {
            if (!e.target.closest('.user-profile')) {
                dropdown.classList.remove('show');
            }
        }
    });

    // 3. Auto-hide alert messages after 3 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s ease';
            setTimeout(() => alert.remove(), 500);
        }, 3000);
    });
});