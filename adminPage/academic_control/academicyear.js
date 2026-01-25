/**
 * Academic_Control/academicyear.js
 * Updated: Improved search to include semester and added validation.
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // 1. IMPROVED Search Filter Logic
    const searchInput = document.querySelector('.search-box input');
    const tableRows = document.querySelectorAll('.data-table tbody tr');

    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();
            
            tableRows.forEach(row => {
                // Kinukuha natin ang text mula sa School Year (1st column) at Semester (2nd column)
                const syText = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                const semText = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                
                // Ipapakita ang row kung ang filter ay tumugma sa taon O sa semester
                if (syText.includes(filter) || semText.includes(filter)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });
    }

    // 2. Form Submission Confirmation & Validation
    const academicForm = document.querySelector('.manual-form');
    if (academicForm) {
        academicForm.addEventListener('submit', function(e) {
            const startYear = parseInt(document.getElementsByName('start_year')[0].value);
            const endYear = parseInt(document.getElementsByName('end_year')[0].value);
            const semester = document.getElementsByName('semester')[0].value;

            // Simple Validation: Siguraduhin na hindi mas maaga ang End Year sa Start Year
            if (endYear < startYear) {
                alert("Error: End Year cannot be earlier than Start Year.");
                e.preventDefault();
                return;
            }

            const confirmMsg = `Are you sure? This will set S.Y. ${startYear}-${endYear} (${semester}) as ACTIVE system-wide.`;
            
            if (!confirm(confirmMsg)) {
                e.preventDefault(); //
            }
        });
    }

    // 3. Simple Alert Auto-hide
    const alertBox = document.querySelector('.alert-box');
    if (alertBox) {
        setTimeout(() => {
            alertBox.style.transition = "opacity 0.5s ease";
            alertBox.style.opacity = "0";
            setTimeout(() => alertBox.remove(), 500);
        }, 4000); // Mawawala ang success message pagkatapos ng 4 seconds
    }
});