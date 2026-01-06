
/**
 * Handle PDF conversion and download using html2pdf.js
 */
function downloadGradePDF() {
            const element = document.getElementById('grade-content');
            const opt = {
                margin: 0.3,
                filename: 'Student_Grades.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        }