/**
 * Studentmanagement.js
 * Handles UI logic for Account Activation
 */

document.addEventListener('DOMContentLoaded', function() {
    // Tawagin agad ang function para ma-set ang initial state ng labels
    updateIDLabel();

    // Makinig sa pagbabago ng Role Dropdown
    const roleSelect = document.getElementById('roleSelect');
    if (roleSelect) {
        roleSelect.addEventListener('change', updateIDLabel);
    }
});

function updateIDLabel() {
    const roleSelect = document.getElementById('roleSelect');
    const label = document.getElementById('idLabel');
    const input = document.getElementsByName('id_number')[0];

    if (!roleSelect || !label || !input) return;

    const role = roleSelect.value;

    // Logic para sa pagpapalit ng Labels at Placeholders
    if (role === 'Student') {
        label.innerText = 'ASSIGN STUDENT NUMBER';
        input.placeholder = 'e.g. 2024-0001-ISCP';
    } else if (role === 'Professor') {
        label.innerText = 'ASSIGN FACULTY ID';
        input.placeholder = 'e.g. PROF-2024-001';
    } else if (role === 'Staff') {
        label.innerText = 'ASSIGN STAFF ID';
        input.placeholder = 'e.g. STAFF-2024-001';
    }
}