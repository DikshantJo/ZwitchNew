@props([
    'title' => 'Introducing Fashion revolution',
    'description' => 'Discover a world of fashion-forward trends, curated collections, and timeless pieces that inspire. Unleash your inner fashionista and embark on a journey of confidence, elegance and impeccable style.',
    'highlightedWord' => 'revolution',
    'highlightedColor' => '#3e9ad9',
    'images' => [
        '/themes/shop/default/build/assets/hero-image-BlVTwpsv.jpg',
        '/themes/shop/default/build/assets/hero-image-DwKg3YHc.webp',
        '/themes/shop/default/build/assets/large-product-placeholder-B9xoAuKQ.webp',
        '/themes/shop/default/build/assets/medium-product-placeholder-INODB-G2.webp'
    ]
])

<div class="fashion-revolution-section">
    <div class="fashion-revolution-container">
        <div class="fashion-revolution-content">
            <!-- Main Title -->
            <div class="fashion-revolution-title">
                <h2>
                    <span>Introducing </span>
                    <span>Fashion </span>
                    <span class="highlighted-text" style="color: {{ $highlightedColor }}">{{ $highlightedWord }}</span>
                </h2>
            </div>
            
            <!-- Content Row -->
            <div class="fashion-revolution-row">
                <!-- Image Carousel -->
                <div class="fashion-revolution-carousel">
                    <div class="carousel-container">
                        @foreach($images as $index => $image)
                            <div class="carousel-slide {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}">
                                <img src="{{ $image }}" alt="Fashion Revolution Image {{ $index + 1 }}" loading="lazy">
                            </div>
                        @endforeach
                        
                        <!-- Carousel Indicators -->
                        <div class="carousel-indicators">
                            @foreach($images as $index => $image)
                                <button class="carousel-indicator {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}" aria-label="Go to slide {{ $index + 1 }}"></button>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Text Content -->
                <div class="fashion-revolution-text">
                    <div class="text-block">
                        <p>{{ $description }}</p>
                    </div>
                    <div class="text-block">
                        <p>{{ $description }}</p>
                    </div>
                    <div class="text-block">
                        <p>{{ $description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.querySelector('.fashion-revolution-carousel');
    if (!carousel) return;
    
    const slides = carousel.querySelectorAll('.carousel-slide');
    const indicators = carousel.querySelectorAll('.carousel-indicator');
    let currentIndex = 0;
    let interval;
    
    function showSlide(index) {
        // Hide all slides
        slides.forEach(slide => slide.classList.remove('active'));
        indicators.forEach(indicator => indicator.classList.remove('active'));
        
        // Show current slide
        slides[index].classList.add('active');
        indicators[index].classList.add('active');
        
        currentIndex = index;
    }
    
    function nextSlide() {
        const nextIndex = (currentIndex + 1) % slides.length;
        showSlide(nextIndex);
    }
    
    function startCarousel() {
        interval = setInterval(nextSlide, 4000); // 4 seconds
    }
    
    function stopCarousel() {
        clearInterval(interval);
    }
    
    // Add click handlers to indicators
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            showSlide(index);
            stopCarousel();
            startCarousel(); // Restart the timer
        });
    });
    
    // Pause on hover
    carousel.addEventListener('mouseenter', stopCarousel);
    carousel.addEventListener('mouseleave', startCarousel);
    
    // Start the carousel
    startCarousel();
});
</script> 