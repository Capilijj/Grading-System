document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('gradeTableBody');
    const rows = tableBody.querySelectorAll('tr[data-student-id]');

    // 1. PUP WEIGHTED CALCULATION (Live Preview)
    rows.forEach(row => {
        const inputs = row.querySelectorAll('.grade-input');
        const avgDisplay = row.querySelector('.final-grade');

        function previewCalculate() {
            const p = parseFloat(inputs[0].value) || 0; // Prelim
            const m = parseFloat(inputs[1].value) || 0; // Midterm
            const f = parseFloat(inputs[2].value) || 0; // Finals
            
            // PUP Formula: (P * 0.30) + (M * 0.30) + (F * 0.40)
            const weightedAvg = (p * 0.30) + (m * 0.30) + (f * 0.40);
            
            avgDisplay.textContent = weightedAvg > 0 ? weightedAvg.toFixed(2) : "--";
            
            // PUP Passing logic (Halimbawa: 3.00 pababa ang pasa sa transmuted scale)
            // Pero kung raw score ito (0-100), kadalasan 75 pataas ang pasa.
            avgDisplay.style.color = weightedAvg > 75 ? "#27ae60" : "#cc0000";
        }

        inputs.forEach(input => input.addEventListener('input', previewCalculate));
        previewCalculate(); 
    });

    // 2. SAVE LOGIC (AJAX)
    tableBody.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('btn-row-save')) {
            const btn = e.target;
            const row = btn.closest('tr');
            const inputs = row.querySelectorAll('.grade-input');
            
            const gradeData = [{
                id: row.getAttribute('data-student-id'),
                prelim: parseFloat(inputs[0].value) || 0,
                midterm: parseFloat(inputs[1].value) || 0,
                finals: parseFloat(inputs[2].value) || 0
            }];

            btn.disabled = true;
            btn.innerText = "...";

            fetch('save_grades_handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(gradeData)
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    btn.style.background = "#2ecc71";
                    btn.innerText = "Saved!";
                    setTimeout(() => location.reload(), 500);
                } else {
                    alert("Error: " + data.message);
                    btn.disabled = false;
                    btn.innerText = "Save";
                }
            })
            .catch(err => {
                alert("Network Error");
                btn.disabled = false;
                btn.innerText = "Save";
            });
        }
    });
});