document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('studentSearch');
    const tableBody = document.getElementById('studentTableBody');
    const noResults = document.getElementById('noResults');

    if (searchInput && tableBody) {
        const rows = tableBody.querySelectorAll('tr:not(#noResults)');

        searchInput.addEventListener('input', function() {
            const query = searchInput.value.toLowerCase();
            let hasMatch = false;

            rows.forEach(row => {
                const name = row.querySelector('.name-col').textContent.toLowerCase();
                const id = row.querySelector('.id-col').textContent.toLowerCase();

                if (name.includes(query) || id.includes(query)) {
                    row.style.display = "";
                    hasMatch = true;
                } else {
                    row.style.display = "none";
                }
            });

            // Ipakita ang "No students found" row kung walang nahanap
            noResults.style.display = hasMatch ? "none" : "";
        });
    }
});