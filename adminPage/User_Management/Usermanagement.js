document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('roleSelect');
    const statusSelect = document.getElementById('statusSelect');
    const studentDiv = document.getElementById('studentSpecific');
    const profDiv = document.getElementById('profDept');
    const academicSection = document.getElementById('academicSection');
    const idLabel = document.getElementById('idLabel');
    const form = document.getElementById('registrationForm');
    
    // Personal Info Sections
    const dobField = document.querySelector('input[name="dob"]');
    const sexField = document.querySelector('select[name="sex"]');
    const phoneField = document.querySelector('input[name="phone"]');
    const streetField = document.querySelector('input[name="street"]');
    const cityField = document.querySelector('input[name="city"]');
    const zipField = document.querySelector('input[name="zip"]');
    const mNameField = document.querySelector('input[name="mName"]');
    
    // Get parent containers
    const dobFieldParent = dobField?.closest('.field');
    const sexFieldParent = sexField?.closest('.field');
    const phoneFieldParent = phoneField?.closest('.field');
    const streetFieldParent = streetField?.closest('.field');
    const cityFieldParent = cityField?.closest('.field');
    const zipFieldParent = zipField?.closest('.field');
    const mNameFieldParent = mNameField?.closest('.field');
    
    // Filtering Elements
    const courseSelect = document.getElementById('courseSelect');
    const sectionSelect = document.getElementById('sectionSelect');
    const deptInput = document.querySelector('input[name="department"]');

    const updateUI = () => {
        const role = roleSelect.value;
        
        // Reset visibility
        studentDiv.style.display = 'none';
        profDiv.style.display = 'none';
        academicSection.style.display = 'block';
        
        // Show all personal fields by default
        if (dobFieldParent) dobFieldParent.style.display = 'block';
        if (sexFieldParent) sexFieldParent.style.display = 'block';
        if (phoneFieldParent) phoneFieldParent.style.display = 'block';
        if (streetFieldParent) streetFieldParent.style.display = 'block';
        if (cityFieldParent) cityFieldParent.style.display = 'block';
        if (zipFieldParent) zipFieldParent.style.display = 'block';
        if (mNameFieldParent) mNameFieldParent.style.display = 'block';
        
        statusSelect.innerHTML = '';
        
        // Remove required attributes from hidden fields
        if (courseSelect) courseSelect.removeAttribute('required');
        if (sectionSelect) sectionSelect.removeAttribute('required');
        if (deptInput) deptInput.removeAttribute('required');
        if (dobField) dobField.removeAttribute('required');
        if (sexField) sexField.removeAttribute('required');

        if (role === 'Student') {
            idLabel.innerText = 'STUDENT ID NUMBER';
            studentDiv.style.display = 'block';
            statusSelect.innerHTML = '<option value="Regular">Regular</option><option value="Irregular">Irregular</option>';
            
            // Add required for student-specific fields
            if (courseSelect) courseSelect.setAttribute('required', 'required');
            if (sectionSelect) sectionSelect.setAttribute('required', 'required');
            if (dobField) dobField.setAttribute('required', 'required');
            if (sexField) sexField.setAttribute('required', 'required');
        } 
        else if (role === 'Professor') {
            idLabel.innerText = 'FACULTY ID NUMBER';
            profDiv.style.display = 'block';
            statusSelect.innerHTML = '<option value="Active (Full-time)">Active (Full-time)</option><option value="Active (Part-time)">Active (Part-time)</option><option value="Inactive">Inactive</option><option value="On-Leave">On-Leave</option>';
            
            // Add required for professor fields
            if (deptInput) deptInput.setAttribute('required', 'required');
            if (dobField) dobField.setAttribute('required', 'required');
            if (sexField) sexField.setAttribute('required', 'required');
        } 
        else if (role === 'Staff') {
            idLabel.innerText = 'STAFF USERNAME';
            academicSection.style.display = 'none';
            statusSelect.innerHTML = '<option value="Active">Active</option>';
            
            // HIDE personal detail fields for Staff
            if (dobFieldParent) dobFieldParent.style.display = 'none';
            if (sexFieldParent) sexFieldParent.style.display = 'none';
            if (phoneFieldParent) phoneFieldParent.style.display = 'none';
            if (streetFieldParent) streetFieldParent.style.display = 'none';
            if (cityFieldParent) cityFieldParent.style.display = 'none';
            if (zipFieldParent) zipFieldParent.style.display = 'none';
            if (mNameFieldParent) mNameFieldParent.style.display = 'none';
        }
    };

    // CLIENT-SIDE VALIDATION BEFORE SUBMIT
    const validateForm = (e) => {
        const role = roleSelect.value;
        const errors = [];
        
        // Check role selection
        if (!role) {
            errors.push("Please select a role");
        }
        
        // Validate ID Number
        const idNumber = document.querySelector('input[name="id_number"]').value.trim();
        if (!idNumber) {
            errors.push("ID Number is required");
        }
        
        // Validate Email
        const email = document.querySelector('input[name="email"]').value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email || !emailRegex.test(email)) {
            errors.push("Valid email address is required");
        }
        
        // Validate Password
        const password = document.querySelector('input[name="password"]').value;
        if (!password) {
            errors.push("Password is required");
        }
        
        // Role-specific validation
        if (role === 'Student') {
            const course = courseSelect ? courseSelect.value : '';
            const section = sectionSelect ? sectionSelect.value : '';
            
            if (!course || course === "" || course === "0") {
                errors.push("Please select a course for the student");
            }
            if (!section || section === "" || section === "0") {
                errors.push("Please select a section for the student");
            }
        }
        
        if (role === 'Professor') {
            const dept = deptInput ? deptInput.value.trim() : '';
            
            if (!dept) {
                errors.push("Please enter the professor's department");
            }
        }
        
        // Display errors if any
        if (errors.length > 0) {
            e.preventDefault();
            alert("⚠️ Please fix the following errors:\n\n" + errors.join("\n"));
            return false;
        }
        
        // Confirm before submit
        const confirmMsg = `Are you sure you want to create this ${role} account?\n\nID: ${idNumber}`;
        if (!confirm(confirmMsg)) {
            e.preventDefault();
            return false;
        }
        
        return true;
    };

    // EVENT LISTENERS
    
    // 1. Role selection change
    roleSelect.addEventListener('change', updateUI);
    
    // 2. Form submission validation
    form.addEventListener('submit', validateForm);
    
    // 3. Real-time validation hints
    const passwordInput = document.getElementById('passwordInput');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const helperText = this.nextElementSibling;
            if (this.value.length > 0) {
                helperText.style.color = '#27ae60';
                helperText.textContent = `Password set (${this.value.length} characters) ✓`;
            } else {
                helperText.style.color = '#999';
                helperText.textContent = 'Any length is accepted';
            }
        });
    }
    
    // 4. Highlight required fields on focus
    const requiredInputs = form.querySelectorAll('[required]');
    requiredInputs.forEach(input => {
        input.addEventListener('invalid', function(e) {
            e.preventDefault();
            this.style.borderColor = '#e74c3c';
        });
        
        input.addEventListener('input', function() {
            if (this.validity.valid) {
                this.style.borderColor = '#27ae60';
            } else {
                this.style.borderColor = '#e74c3c';
            }
        });
        
        input.addEventListener('blur', function() {
            if (this.value === '') {
                this.style.borderColor = '#dce4ec';
            }
        });
    });

    // Initial UI setup
    updateUI();
});