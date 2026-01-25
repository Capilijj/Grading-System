document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('gradeTableBody');
    const searchInput = document.getElementById('gradeSearch');

    // 1. FUNCTION: GET REMARKS LOGIC
    // Ginagamit ito para sa "Live" update ng UI
    function updateRowRemarks(inputElement) {
        const row = inputElement.closest('tr');
        const remarksCell = row.querySelector('.remarks-col');
        if (!remarksCell) return;

        const val = inputElement.value.toUpperCase().trim();
        const numVal = parseFloat(val);

        // Reset classes/styles
        remarksCell.className = "text-center remarks-col"; 
        remarksCell.style.fontWeight = "bold";

        if (val === "") {
            remarksCell.innerText = "";
        } else if (val === "INC") {
            remarksCell.innerText = "INCOMPLETE";
            remarksCell.style.color = "#e67e22"; // Orange
        } else if (val === "W") {
            remarksCell.innerText = "WITHDRAWN";
            remarksCell.style.color = "#7f8c8d"; // Gray
        } else if (!isNaN(numVal)) {
            if (numVal >= 1.0 && numVal <= 3.0) {
                remarksCell.innerText = "PASSED";
                remarksCell.style.color = "#27ae60"; // Green
            } else if (numVal > 3.0 && numVal <= 5.0) {
                remarksCell.innerText = "FAILED";
                remarksCell.style.color = "#e74c3c"; // Red
            } else {
                remarksCell.innerText = "INVALID";
                remarksCell.style.color = "#ff0000";
            }
        } else {
            remarksCell.innerText = "INVALID";
            remarksCell.style.color = "#ff0000";
        }
    }

    // 2. EVENT LISTENER: LIVE UPDATE HABANG NAGTATYPE
    tableBody.addEventListener('input', function(e) {
        if (e.target.classList.contains('grade-input')) {
            updateRowRemarks(e.target);
        }
    });

    // 3. SEARCH FUNCTIONALITY
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const filter = searchInput.value.toLowerCase();
            const rows = tableBody.querySelectorAll('tr:not(#noResults)');
            
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }

    // 4. SAVE LOGIC (AJAX)
    tableBody.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('btn-row-save')) {
            const btn = e.target;
            const row = btn.closest('tr');
            const input = row.querySelector('.grade-input');
            
            const finalGradeValue = input.value.toUpperCase().trim();
            const numVal = parseFloat(finalGradeValue);
            const validSymbols = ['INC', 'W'];

            // ENGLISH VALIDATION
            if (!validSymbols.includes(finalGradeValue)) {
                if (isNaN(numVal) || numVal < 1.0 || numVal > 5.0) {
                    alert("Invalid Grade! Please enter a value between 1.0 and 5.0, or use INC/W.");
                    input.focus();
                    return;
                }
            }

            // DATA PREPARATION
            const gradeData = [{
                id: row.getAttribute('data-student-id'),
                gradeValue: finalGradeValue,
                subjectID: row.getAttribute('data-subject-id') || 1
            }];

            btn.disabled = true;
            btn.innerText = "Saving...";

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
                    
                    // Permanent color update for input after save
                    if (numVal > 3.0 || finalGradeValue === '5.0') {
                        input.style.color = "#e74c3c"; 
                    } else if (numVal <= 3.0) {
                        input.style.color = "#27ae60"; 
                    }

                    setTimeout(() => {
                        btn.style.background = "#27ae60";
                        btn.innerText = "Save";
                        btn.disabled = false;
                    }, 1500);
                } else {
                    alert("Error: " + data.message);
                    btn.disabled = false;
                    btn.innerText = "Save";
                }
            })
            .catch(err => {
                console.error("Fetch Error:", err);
                alert("Network Error: Could not connect to the server.");
                btn.disabled = false;
                btn.innerText = "Save";
            });
        }
    });
});