@props([
    'title' => 'Introducing revolution',
    'description' => 'Discover a world of fashion-forward trends, curated collections, and timeless pieces that inspire. Unleash your inner fashionista and embark on a journey of confidence, elegance and impeccable style.',
    'highlightedWord' => 'revolution',
    'highlightedColor' => '#3e9ad9'
])

<div class="hero-section">
    <div class="hero-container">
        <!-- Top Description -->
        <div class="hero-description hero-description-top">
            <p>{{ $description }}</p>
        </div>
        
        <!-- Main Title -->
        <div class="hero-title">
            <h1>
                <span>Introducing </span>
                <span class="highlighted-text" style="color: {{ $highlightedColor }}">{{ $highlightedWord }}</span>
            </h1>
        </div>
        
        <!-- Bottom Descriptions -->
        <div class="hero-descriptions-bottom">
            <div class="hero-description hero-description-left">
                <p>{{ $description }}</p>
            </div>
            
            <div class="hero-description hero-description-right">
                <p>{{ $description }}</p>
            </div>
        </div>
    </div>
</div> 