document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('gradeSearch');
    const tableBody = document.getElementById('gradeTableBody');
    const rows = tableBody.querySelectorAll('tr:not(#noResults)');
    const noResults = document.getElementById('noResults');

    // 1. Search Logic
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

        noResults.style.display = hasMatch ? "none" : "";
    });

    // 2. Average Calculator Logic
    rows.forEach(row => {
        const inputs = row.querySelectorAll('.grade-input');
        const avgDisplay = row.querySelector('.final-grade');

        function calculate() {
            let sum = 0;
            let count = 0;
            inputs.forEach(input => {
                if (input.value !== "") {
                    sum += parseFloat(input.value);
                    count++;
                }
            });

            if (count > 0) {
                const avg = (sum / count).toFixed(2);
                avgDisplay.textContent = avg;
                // Red color kung bagsak (halimbawa 3.1 pataas)
                avgDisplay.style.color = avg > 3.0 ? "#cc0000" : "#27ae60";
            } else {
                avgDisplay.textContent = "--";
            }
        }

        inputs.forEach(input => input.addEventListener('input', calculate));
        calculate(); // Run once for placeholder data
    });
});