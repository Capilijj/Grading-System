document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('gradeTableBody');
    const searchInput = document.getElementById('gradeSearch');

    console.log('InputGrades.js loaded'); // DEBUG

    // 1. FUNCTION: GET REMARKS LOGIC
    function updateRowRemarks(inputElement) {
        const row = inputElement.closest('tr');
        const remarksCell = row.querySelector('.remarks-col');
        if (!remarksCell) return;

        const val = inputElement.value.toUpperCase().trim();
        const numVal = parseFloat(val);

        remarksCell.className = "text-center remarks-col"; 
        remarksCell.style.fontWeight = "bold";

        if (val === "") {
            remarksCell.innerText = "";
        } else if (val === "INC") {
            remarksCell.innerText = "INCOMPLETE";
            remarksCell.style.color = "#e67e22";
        } else if (val === "W") {
            remarksCell.innerText = "WITHDRAWN";
            remarksCell.style.color = "#7f8c8d";
        } else if (!isNaN(numVal)) {
            if (numVal >= 1.0 && numVal <= 3.0) {
                remarksCell.innerText = "PASSED";
                remarksCell.style.color = "#27ae60";
            } else if (numVal > 3.0 && numVal <= 5.0) {
                remarksCell.innerText = "FAILED";
                remarksCell.style.color = "#e74c3c";
            } else {
                remarksCell.innerText = "INVALID";
                remarksCell.style.color = "#ff0000";
            }
        } else {
            remarksCell.innerText = "INVALID";
            remarksCell.style.color = "#ff0000";
        }
    }

    // 2. LIVE UPDATE
    tableBody.addEventListener('input', function(e) {
        if (e.target.classList.contains('grade-input')) {
            updateRowRemarks(e.target);
        }
    });

    // 3. SEARCH
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

    // 4. SAVE LOGIC WITH DEBUG
    tableBody.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('btn-row-save')) {
            const btn = e.target;
            const row = btn.closest('tr');
            const input = row.querySelector('.grade-input');
            
            const studentID = row.getAttribute('data-student-id');
            const subjectID = row.getAttribute('data-subject-id');
            const finalGradeValue = input.value.toUpperCase().trim();
            const numVal = parseFloat(finalGradeValue);
            const validSymbols = ['INC', 'W'];

            console.log('Save clicked:', { studentID, subjectID, finalGradeValue }); // DEBUG

            // VALIDATION
            if (!validSymbols.includes(finalGradeValue)) {
                if (isNaN(numVal) || numVal < 1.0 || numVal > 5.0) {
                    alert("Invalid Grade! Please enter a value between 1.0 and 5.0, or use INC/W.");
                    input.focus();
                    return;
                }
            }

            // Check if subjectID exists
            if (!subjectID || subjectID === 'null' || subjectID === '') {
                alert("Error: Subject ID is missing. Please refresh the page.");
                console.error('Missing subjectID in row:', row);
                return;
            }

            // DATA PREPARATION
            const gradeData = [{
                id: studentID,
                gradeValue: finalGradeValue,
                subjectID: parseInt(subjectID)
            }];

            console.log('Sending data:', gradeData); // DEBUG

            btn.disabled = true;
            btn.innerText = "Saving...";

            fetch('save_grades_handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(gradeData)
            })
            .then(res => {
                console.log('Response status:', res.status); // DEBUG
                return res.json();
            })
            .then(data => {
                console.log('Server Response:', data); // DEBUG
                
                if(data.status === 'success') {
                    btn.style.background = "#2ecc71";
                    btn.innerText = "Saved!";
                    
                    // Permanent color update
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
                    console.error('Save failed:', data);
                    alert("Error: " + (data.message || 'Unknown error') + "\n\nCheck browser console (F12) for details");
                    btn.disabled = false;
                    btn.innerText = "Save";
                }
            })
            .catch(err => {
                console.error("Fetch Error:", err);
                alert("Network Error: " + err.message + "\n\nCheck browser console (F12) for details");
                btn.disabled = false;
                btn.innerText = "Save";
            });
        }
    });
});