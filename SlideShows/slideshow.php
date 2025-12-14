<?php
// ================================
// Set paths for images and assets
// ================================
$image_base_path = '../image/'; // Base folder for images
$asset_path = './';             // Base folder for other assets (CSS/JS)
?>

<!-- Link to the CSS file for slideshow styling -->
<link rel="stylesheet" href="SlideShows/slideshow.css"> 

<!-- ================================
     Slideshow / Image Section
     ================================ -->
<div class="image-section">

    <!-- Container for all slideshow images -->
    <div class="slideshow-container">
        <!-- Each image represents a slide -->
        <!-- First image is active by default -->
        <img src="<?php echo $image_base_path; ?>bg.gif" alt="University Image" class="mySlides active-slide"> 
        <img src="<?php echo $image_base_path; ?>bg1.jpg" alt="University Image 1" class="mySlides"> 
        <img src="<?php echo $image_base_path; ?>bg2.jpg" alt="University Image 2" class="mySlides"> 
        <img src="<?php echo $image_base_path; ?>bg3.jpg" alt="University Image 3" class="mySlides"> 
        <img src="<?php echo $image_base_path; ?>bg4.png" alt="University Image 4" class="mySlides"> 
        <img src="<?php echo $image_base_path; ?>bg5.webp" alt="University Image 5" class="mySlides"> 
    </div>

    <!-- ================================
         Slider Dots / Navigation
         ================================ -->
    <div class="slider-dots">
        <!-- Each dot corresponds to a slide -->
        <!-- Clicking a dot jumps to that slide -->
        <span class="dot active-dot" onclick="currentSlide(1)"></span>
        <span class="dot" onclick="currentSlide(2)"></span>
        <span class="dot" onclick="currentSlide(3)"></span>
        <span class="dot" onclick="currentSlide(4)"></span>
        <span class="dot" onclick="currentSlide(5)"></span>
        <span class="dot" onclick="currentSlide(6)"></span>
    </div>
</div>

<!-- JavaScript file that controls slideshow behavior -->
<script src="SlideShows/slideshow.js"></script>
