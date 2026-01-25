/**
 * Handle PDF conversion and download using html2pdf.js
 */
function downloadGradePDF() {
    // Kinukuha ang main container ng grades
    const element = document.getElementById('grade-content');

    // Error handling: Check kung existing ang element para iwas "null" error
    if (!element) {
        console.error("Error: Element #grade-content not found. Check your HTML ID.");
        return;
    }

    // Optional: Itago ang download button sa loob ng PDF para mas malinis
    const downloadBtn = document.querySelector('.btn-download');
    if (downloadBtn) downloadBtn.style.visibility = 'hidden';

    const opt = {
        margin: [0.3, 0.3, 0.5, 0.3], // Top, Left, Bottom, Right
        filename: 'Student_Grades_ISCP.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { 
            scale: 2, 
            useCORS: true, // Importante ito para sa mga images/logos
            letterRendering: true,
            scrollY: 0
        },
        jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
    };

    // Pag-execute ng conversion
    html2pdf()
        .set(opt)
        .from(element)
        .save()
        .then(() => {
            // Ibalik ang button pagkatapos ma-generate ang PDF
            if (downloadBtn) downloadBtn.style.visibility = 'visible';
            console.log("PDF downloaded successfully.");
        })
        .catch(err => {
            console.error("html2pdf Error: ", err);
            if (downloadBtn) downloadBtn.style.visibility = 'visible';
        });
}

/**
 * Auto-initialization on Page Load
 */
document.addEventListener('DOMContentLoaded', () => {
    console.log("Grade JavaScript initialized for student: " + document.querySelector('.student-name-id')?.innerText);
});