// FILE: slideshow.js
// Slideshow logic (Reusable Component)

let slides = document.getElementsByClassName("mySlides");
let dots = document.getElementsByClassName("dot");
let slideIndex = 1; 

// Function to show a specific slide
function showSlides(n) {
    let i;
    
    // Handle loop: if index is out of bounds, wrap around
    if (n > slides.length) {slideIndex = 1}    
    if (n < 1) {slideIndex = slides.length}
    
    // Hide all slides and remove active class from dots
    for (i = 0; i < slides.length; i++) {
        slides[i].classList.remove("active-slide");
        dots[i].classList.remove("active-dot"); 
    }

    // Show the current slide and activate the current dot
    slides[slideIndex - 1].classList.add("active-slide");
    dots[slideIndex - 1].classList.add("active-dot");
}

// Function for automatic sliding
function autoSlide() {
    // Stop if no slides are found
    if (slides.length === 0) return;

    slideIndex++;
    showSlides(slideIndex);
    
    // Call autoSlide again after 3000 milliseconds (3 seconds)
    setTimeout(autoSlide, 2500); 
}

// Function called when a dot is clicked
function currentSlide(n) {
    slideIndex = n;
    showSlides(slideIndex);
}

// Initial call to start the slideshow when the page loads
document.addEventListener("DOMContentLoaded", () => {
    // Start with the first slide visible
    showSlides(slideIndex);
    
    // Start the automatic rotation after the initial delay
    setTimeout(autoSlide, 3000); 
});