/**
 * Professormanagement.js
 * Pinamamahalaan ang Edit at Modal logic para sa Professor Management
 */

// Kunin ang role ng user para sa access control (base sa UI)
const CURRENT_USER_ROLE = document.querySelector('p b') ? document.querySelector('p b').innerText : 'Staff';

/**
 * Nagbubukas ng Edit Modal at nilalagyan ng data ang mga fields
 * @param {Object} data - Ang row data mula sa SQL/PHP fetch
 */
function openEditModal(data) {
    console.log("Loading data to modal:", data); // Debugging: Makita sa console ang laman ng data

    // 1. Basic Information
    document.getElementById('edit_id').value = data.professorID || '';
    document.getElementById('edit_fname').value = data.fName || '';
    
    // Case sensitivity fix: Sinusubukan ang 'mname' at 'mName'
    document.getElementById('edit_mname').value = data.mname || data.mName || '';
    document.getElementById('edit_lname').value = data.lName || '';

    // 2. Employment & Department
    document.getElementById('edit_dept').value = data.department || '';
    document.getElementById('edit_status').value = data.employmentStatus || 'Active (Full-time)';

    // 3. Contact Details
    document.getElementById('edit_email').value = data.email || '';
    document.getElementById('edit_phone').value = data.phoneNumber || '';

    // 4. Address Details (Fix para sa 'unde' / Truncated error)
    document.getElementById('edit_street').value = data.street || '';
    document.getElementById('edit_city').value = data.city || '';
    
    // Sinusubukan ang lahat ng posibleng key names para sa Zip Code
    let zipValue = data.zipcode || data.zipCode || data.zip_code || '';
    document.getElementById('edit_zip').value = zipValue;

    // 5. Date of Birth (Dapat format ay YYYY-MM-DD)
    if (data.dateOfBirth) {
        document.getElementById('edit_dob').value = data.dateOfBirth;
    }

    // 6. Password Field
    // Karaniwang iniiwan itong blangko para sa security, 
    // pero dahil 'required' ito sa iyong HTML, kailangang mag-input ang user.
    const passField = document.querySelector('input[name="pass"]');
    if (passField) passField.value = ''; 

    // Ipakita ang Modal
    document.getElementById('editModal').style.display = 'flex';
}

/**
 * Nagsasara ng modal base sa ID
 * @param {string} modalId 
 */
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

/**
 * Close modal kapag clinic sa labas ng box
 */
window.onclick = function(event) {
    const editModal = document.getElementById('editModal');
    if (event.target == editModal) {
        closeModal('editModal');
    }
}