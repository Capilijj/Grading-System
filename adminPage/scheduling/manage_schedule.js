document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('schedSearch');
    const tableRows = document.querySelectorAll('#schedTable tbody tr');

    if(searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            tableRows.forEach(row => {
                // I-filter base sa Professor Name at Course Code
                const textContent = row.textContent.toLowerCase();
                row.style.display = textContent.includes(query) ? "" : "none";
            });
        });
    }
});