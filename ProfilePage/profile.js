document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('fileInput');
    const profileDisplay = document.getElementById('profileDisplay');

    // Kapag pumili ng file si user
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Papalitan nito yung src ng <img> tag para makita yung preview
                    profileDisplay.setAttribute('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });
    }
});