{{-- Christmas Snowflakes Component --}}
{{-- Simple canvas-based snowflake effect that works independently --}}

<div id="christmas-snowflakes" class="snowflakes-container" aria-hidden="true">
    <canvas id="snowflakes-canvas"></canvas>
</div>

<style>
/* Christmas Snowflakes - Canvas-based */
.snowflakes-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 9999;
    overflow: hidden;
}

#snowflakes-canvas {
    display: block;
    width: 100%;
    height: 100%;
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    #snowflakes-canvas {
        display: none;
    }
}
</style>

<script>
(function() {
    'use strict';
    
    // VERSION TEST - This should show if the file is updated
    console.log('Snowflake component version: CLEAN - ' + new Date().toISOString());
    
    // Simple snowflake system - works independently
    function initSnowflakes() {
        const canvas = document.getElementById('snowflakes-canvas');
        if (!canvas) {
            return;
        }
        
        const ctx = canvas.getContext('2d');
        let dropsArray = [];
        let animationId = null;
        
        function setDimensions() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        
        function drop() {
            this.x = Math.random() * canvas.width;
            this.y = -Math.random() * 500;    // Starting point (top of screen)
            this.r = 0.2 + 1.5 * Math.random();
            this.s = Math.random() + 0.5; // Speed
            this.d = Math.random() * canvas.width;  // The distance the snowflake will travel
            this.draw = () => {
                ctx.beginPath();
                ctx.fillStyle = '#ECE9E6';
                ctx.shadowColor = '#ffffff30';
                ctx.shadowOffsetX = -2;
                ctx.shadowOffsetY = -2;
                ctx.shadowBlur = 2;
                ctx.arc(this.x, this.y, this.r, 0, 2 * Math.PI, true);
                ctx.fill();
                this.y += this.s;   // Snowflakes falling
                this.x += Math.sin(this.y / this.d) * 2;   // Snowflakes swinging side to side
                if (this.y >= canvas.height) {
                    this.y = -10;    // Re-positioning fallen snowflake to "respawn" it.
                }
            };
        }
        
        function drops() {
            dropsArray = [];
            // Calculate snowflake count based on screen size
            const width = window.innerWidth;
            const height = window.innerHeight;
            let snowflakeCount;
            
            if (width <= 480) {
                snowflakeCount = Math.min(50, Math.floor((width * height) / 8000));
            } else if (width <= 768) {
                snowflakeCount = Math.min(100, Math.floor((width * height) / 6000));
            } else {
                snowflakeCount = Math.min(200, Math.floor((width * height) / 4000));
            }
            
            for (let i = 0; i < snowflakeCount; i++) {
                dropsArray[i] = new drop();
            }
        }
        
        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            dropsArray.forEach((drop) => drop.draw());
            animationId = requestAnimationFrame(animate);
        }
        
        // Initialize
        setDimensions();
        drops();
        animate();
        
        // Handle resize
        window.addEventListener('resize', () => {
            setDimensions();
            drops();
        });
        
        // Handle visibility change
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                if (animationId) {
                    cancelAnimationFrame(animationId);
                    animationId = null;
                }
            } else {
                if (!animationId) {
                    animate();
                }
            }
        });
        
        // Handle reduced motion
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            if (animationId) {
                cancelAnimationFrame(animationId);
                animationId = null;
            }
        }
        
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSnowflakes);
    } else {
        initSnowflakes();
    }
    
    // Also try to initialize after a short delay to ensure everything is loaded
    setTimeout(initSnowflakes, 1000);
    
})();
</script> 