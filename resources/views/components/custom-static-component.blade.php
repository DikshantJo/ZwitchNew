@props([
    'title' => 'Default Title',
    'description' => 'Default description',
    'image' => null,
    'buttonText' => 'Learn More',
    'buttonUrl' => '#'
])

<div class="custom-static-component">
    <div class="component-container">
        @if($image)
            <div class="component-image">
                <img src="{{ $image }}" alt="{{ $title }}" loading="lazy">
            </div>
        @endif
        
        <div class="component-content">
            <h3 class="component-title">{{ $title }}</h3>
            <p class="component-description">{{ $description }}</p>
            <a href="{{ $buttonUrl }}" class="component-button">
                {{ $buttonText }}
            </a>
        </div>
    </div>
</div> 