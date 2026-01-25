document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('studentSearch');
    const tableBody = document.getElementById('studentTableBody');
    const noResults = document.getElementById('noResults');

    if (searchInput && tableBody) {
        searchInput.addEventListener('input', function() {
            const query = searchInput.value.toLowerCase().trim();
            // Kunin ang lahat ng tr maliban sa noResults at yung empty message row mula sa PHP
            const rows = tableBody.querySelectorAll('tr:not(#noResults)');
            let hasMatch = false;
            let visibleCount = 0;

            rows.forEach(row => {
                // I-check kung ang row ay hindi yung "No students found" message na galing PHP
                const nameCol = row.querySelector('.name-col');
                const idCol = row.querySelector('.id-col');

                if (nameCol && idCol) {
                    const name = nameCol.textContent.toLowerCase();
                    const id = idCol.textContent.toLowerCase();

                    if (name.includes(query) || id.includes(query)) {
                        row.style.display = "";
                        hasMatch = true;
                        visibleCount++;
                    } else {
                        row.style.display = "none";
                    }
                }
            });

            // Ipakita ang "No results" row kung walang tumugma sa search
            if (noResults) {
                noResults.style.display = (query !== "" && !hasMatch) ? "" : "none";
            }
        });
    }
});