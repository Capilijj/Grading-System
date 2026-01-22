/**
 * schedule.js
 * Handle PDF conversion and download using html2pdf.js
 */

function downloadPDF() {
    // Kinukuha ang element na may ID na 'schedule-content'
    const element = document.getElementById('schedule-content');
    
    // Settings para sa itsura ng PDF
    const opt = {
        margin:       0.3,
        filename:     'My_Class_Schedule.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { 
            scale: 2, 
            useCORS: true, 
            logging: false 
        },
        jsPDF:        { 
            unit: 'in', 
            format: 'letter', 
            orientation: 'portrait' 
        }
    };

    // Instruction para simulan ang download
    html2pdf().set(opt).from(element).save();
}