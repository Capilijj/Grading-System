document.addEventListener("DOMContentLoaded", () => {
    const navItems = document.querySelectorAll(".nav-item");

    navItems.forEach(item => {
        item.addEventListener("click", function() {
            // Tanggalin ang active class sa lahat
            navItems.forEach(nav => nav.classList.remove("active"));
            // Idagdag sa pinindot
            this.classList.add("active");
        });
    });
});