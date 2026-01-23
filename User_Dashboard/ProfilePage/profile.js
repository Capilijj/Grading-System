// profile.js
document.addEventListener('DOMContentLoaded', function() {
    const toast = document.getElementById('toast');
    const closeBtn = document.getElementById('closeToast');

    // Kapag may status na (success or error), i-auto hide pagkatapos ng 4 seconds
    if (toast.classList.contains('success') || toast.classList.contains('error')) {
        setTimeout(() => {
            hideToast();
        }, 4000);
    }

    // Manual close button
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            hideToast();
        });
    }

    function hideToast() {
        toast.style.transform = "translateX(120%)";
        setTimeout(() => {
            toast.style.visibility = "hidden";
        }, 400);
    }
});