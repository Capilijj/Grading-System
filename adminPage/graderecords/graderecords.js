document.addEventListener("DOMContentLoaded", () => {
    const tabs = document.querySelectorAll('.tab-btn');
    const rows = document.querySelectorAll('.grade-row-item');
    const searchInput = document.getElementById('studentSearch');
    const courseTitle = document.getElementById('activeCourseTitle');
    const modal = document.getElementById('gradeModal');
    const closeBtn = document.getElementById('closeModalBtn');

    let currentCourse = "BSCS";

    // FILTER FUNCTION
    function filterData() {
        const searchTerm = searchInput.value.toLowerCase();
        
        rows.forEach(row => {
            const courseMatch = row.getAttribute('data-course') === currentCourse;
            const name = row.querySelector('.student-name').textContent.toLowerCase();
            const searchMatch = name.includes(searchTerm);

            if (courseMatch && searchMatch) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }

    // TAB CLICKS
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            
            currentCourse = tab.getAttribute('data-filter');
            courseTitle.innerText = currentCourse;
            searchInput.value = ""; // Reset search field
            filterData();
        });
    });

    // SEARCH INPUT
    searchInput.addEventListener('input', filterData);

    // MODAL CONTROL
    window.openUpdateModal = (name, id) => {
        document.getElementById("modalStudentName").innerText = name;
        document.getElementById("modalStudentID").innerText = id;
        modal.style.display = "flex";
    };

    closeBtn.onclick = () => modal.style.display = "none";
    window.onclick = (e) => { if (e.target == modal) modal.style.display = "none"; };
});