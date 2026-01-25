document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('roleSelect');
    const courseSelect = document.querySelector('select[name="course_id"]');
    const sectionSelect = document.querySelector('select[name="section_id"]');

    // 1. ROLE TOGGLE LOGIC (Yung code mo kanina)
    if (roleSelect) {
        roleSelect.addEventListener('change', function() {
            const role = this.value;
            const studentDiv = document.getElementById('studentSpecific');
            const studentYear = document.getElementById('studentYear');
            const profDiv = document.getElementById('profSpecific');
            const profDept = document.getElementById('profDept');
            const idLabel = document.getElementById('idLabel');

            studentDiv.style.display = 'none';
            studentYear.style.display = 'none';
            profDiv.style.display = 'none';
            profDept.style.display = 'none';

            if (role === 'Student') {
                studentDiv.style.display = 'block';
                studentYear.style.display = 'block';
                idLabel.innerText = 'STUDENT ID NUMBER';
            } else if (role === 'Professor') {
                profDiv.style.display = 'block';
                profDept.style.display = 'block';
                idLabel.innerText = 'FACULTY ID NUMBER';
            } else {
                idLabel.innerText = 'ID NUMBER';
            }
        });
    }

    // 2. SECTION FILTERING LOGIC (Para iwas Foreign Key Error)
    if (courseSelect && sectionSelect) {
        courseSelect.addEventListener('change', function() {
            const selectedCourseID = this.value; // Kunwari: "1"
            const options = sectionSelect.querySelectorAll('option');

            options.forEach(option => {
                if (option.value === "") {
                    option.style.display = 'block'; // Ipakita ang "Select Section"
                    return;
                }

                // I-check kung ang text ay may "Course Link: X"
                if (option.text.includes(`Course Link: ${selectedCourseID}`)) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });

            // I-reset ang pili sa Section para hindi maiwan yung maling ID
            sectionSelect.value = "";
        });
    }
});