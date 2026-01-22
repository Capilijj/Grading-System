function openEditModal(data) {
    document.getElementById('edit_id').value = data.professorID;
    document.getElementById('edit_fname').value = data.fName;
    document.getElementById('edit_mname').value = data.mName;
    document.getElementById('edit_lname').value = data.lName;
    document.getElementById('edit_dept').value = data.department;
    document.getElementById('edit_email').value = data.email;
    document.getElementById('edit_phone').value = data.phoneNumber;
    document.getElementById('edit_street').value = data.street;
    document.getElementById('edit_city').value = data.city;
    document.getElementById('edit_zip').value = data.zipCode;
    document.getElementById('edit_status').value = data.employmentStatus || 'Active';

    document.getElementById('editModal').style.display = 'flex';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}