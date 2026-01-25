/**
 * Studentmanagement.js
 * Updated with Grade Sheet functionality
 */

const CURRENT_USER_ROLE = document.querySelector('p b').innerText;

/**
 * Opens Edit Modal for Personal Information
 */
function openEditModal(data) {
    console.log("Editing Profile:", data);

    document.getElementById('edit_id').value = data.studentID;
    document.getElementById('edit_fname').value = data.fName || '';
    document.getElementById('edit_mname').value = data.mName || '';
    document.getElementById('edit_lname').value = data.lName || '';
    document.getElementById('edit_sex').value   = data.sex || 'Male';
    document.getElementById('edit_dob').value   = data.dateOfBirth || '';
    document.getElementById('edit_email').value = data.email || '';
    document.getElementById('edit_phone').value = data.phoneNumber || '';
    document.getElementById('edit_street').value = data.street || '';
    document.getElementById('edit_city').value   = data.city || '';
    document.getElementById('edit_zip').value    = data.zipCode || '';
    document.getElementById('edit_course').value = data.courseID || '';
    document.getElementById('edit_status').value = data.status || 'Pending';

    document.getElementById('editModal').style.display = 'flex';
}

/**
 * Opens Grade Sheet Modal - Fetches data via AJAX
 */
function openGradeModal(studentID, studentName, ayID) {
    console.log("Opening Grade Sheet for:", studentID);
    
    document.getElementById('modal_student_name').innerText = `Grade Sheet - ${studentName}`;
    document.getElementById('gradeModal').style.display = 'flex';
    
    // Show loading state
    document.getElementById('grade_sheet_container').innerHTML = 
        '<p style="text-align:center; color:#999;">Loading grade sheet...</p>';
    
    // Fetch grade sheet data
    fetch(`fetch_grade_sheet.php?student_id=${studentID}&ay_id=${ayID}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                document.getElementById('grade_sheet_container').innerHTML = 
                    `<p style="text-align:center; color:#e74c3c;">${data.error}</p>`;
                return;
            }
            
            renderGradeSheet(data, studentID, studentName);
        })
        .catch(error => {
            console.error('Error fetching grade sheet:', error);
            document.getElementById('grade_sheet_container').innerHTML = 
                '<p style="text-align:center; color:#e74c3c;">Failed to load grade sheet.</p>';
        });
}

/**
 * Renders the grade sheet table
 */
function renderGradeSheet(gradeData, studentID, studentName) {
    if (!gradeData || gradeData.length === 0) {
        document.getElementById('grade_sheet_container').innerHTML = 
            '<p style="text-align:center; color:#999;">No subjects found for this student.</p>';
        return;
    }
    
    let html = `
        <table class="grade-sheet-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Subject Code</th>
                    <th>Description</th>
                    <th>Faculty Name</th>
                    <th>Units</th>
                    <th>Final Grade</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    gradeData.forEach((row, index) => {
        html += `
            <tr>
                <td>${index + 1}</td>
                <td><code>${row.subjectCode}</code></td>
                <td>${row.Description}</td>
                <td>${row.FacultyName}</td>
                <td>${row.units}</td>
                <td><strong>${row.FinalGrade || '-'}</strong></td>
                <td><span class="status-badge ${getStatusClass(row.DisplayStatus)}">${row.DisplayStatus || '-'}</span></td>
                <td>
                    <button class="btn-edit-grade" onclick='openEditGradeModal("${studentID}", "${studentName}", "${row.subjectCode}", "${row.Description}", "${row.FinalGrade}", "${row.DisplayStatus}")'>
                        Edit
                    </button>
                </td>
            </tr>
        `;
    });
    
    html += `
            </tbody>
        </table>
    `;
    
    document.getElementById('grade_sheet_container').innerHTML = html;
}

/**
 * Opens Individual Grade Edit Modal
 */
function openEditGradeModal(studentID, studentName, subjectCode, subjectName, currentGrade, currentRemarks) {
    document.getElementById('edit_grade_student_id').value = studentID;
    document.getElementById('edit_grade_subject_id').value = subjectCode;
    document.getElementById('edit_grade_subject_name').innerText = `${subjectCode} - ${subjectName}`;
    document.getElementById('edit_grade_student_name').innerText = studentName;
    document.getElementById('edit_grade_input').value = currentGrade || '';
    document.getElementById('edit_remarks_input').value = currentRemarks || 'ENROLLED';
    
    document.getElementById('editGradeModal').style.display = 'flex';
}

/**
 * Helper function to get CSS class for status badges
 */
function getStatusClass(status) {
    if (!status) return '';
    switch(status.toUpperCase()) {
        case 'P': return 'status-passed';
        case 'F': return 'status-failed';
        case 'W': return 'status-withdrawn';
        case 'INC': return 'status-incomplete';
        default: return '';
    }
}

/**
 * Closes modal by ID
 */
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}

/**
 * Delete Confirmation (Admin Only)
 */
function confirmDelete(id) {
    if (CURRENT_USER_ROLE.trim() !== 'Super Admin') {
        alert("⛔ ACCESS DENIED: Only 'Super Admin' can delete records.");
        return;
    }

    if (confirm("Are you sure you want to permanently delete this student record?")) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="student_id" value="${id}">
            <input type="hidden" name="delete_student" value="1">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Global click listener to close modals when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal-overlay')) {
        event.target.style.display = 'none';
    }
}