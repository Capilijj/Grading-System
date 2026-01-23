/**
 * Studentmanagement.js
 */
const CURRENT_USER_ROLE = document.querySelector('p b').innerText;

function openEditModal(data) {
    // I-verify ang keys base sa SQL columns mo
    document.getElementById('edit_id').value = data.studentID;
    document.getElementById('edit_fname').value = data.fName;
    
    // PAGWAWASTO: Siguraduhing tumutugma sa SQL Column names
    document.getElementById('edit_mname').value = data.mname || ''; // Karaniwang 'mname' sa SQL
    document.getElementById('edit_lname').value = data.lName;
    document.getElementById('edit_course').value = data.courseID; 
    document.getElementById('edit_sex').value = data.sex;
    
    // Siguraduhin na ang format ng data.dateOfBirth ay YYYY-MM-DD
    document.getElementById('edit_dob').value = data.dateOfBirth;
    
    document.getElementById('edit_email').value = data.email;
    document.getElementById('edit_phone').value = data.phoneNumber;
    document.getElementById('edit_street').value = data.street;
    document.getElementById('edit_city').value = data.city;
    document.getElementById('edit_zip').value = data.zipCode; // Case sensitive 'zipCode'
    document.getElementById('edit_status').value = data.status || 'Pending';

    document.getElementById('editModal').style.display = 'flex';
}

function confirmDelete(id) {
    // Paalala: Sa PHP mo, 'Super Admin' ang kailangan para makapag-delete
    if (CURRENT_USER_ROLE !== 'Super Admin' && CURRENT_USER_ROLE !== 'Admin') {
        alert("⛔ ACCESS DENIED: Only Administrators can delete records.");
        return;
    }
    document.getElementById('del_id').value = id;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}