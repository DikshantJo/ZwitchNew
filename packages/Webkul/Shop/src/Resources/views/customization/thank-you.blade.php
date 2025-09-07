<!-- Page Layout -->
<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        Thank You - Customisation Request Submitted
    </x-slot>

    <div class="bg-[#0f0f0f] dark:bg-[#0f0f0f] light:bg-white relative min-h-screen">
        <!-- Hero Section -->
        <div class="container mx-auto px-4 py-16">
            <div class="max-w-4xl mx-auto text-center">
                <!-- Success Icon -->
                <div class="w-24 h-24 bg-[#c2b4a3] rounded-full flex items-center justify-center mx-auto mb-8">
                    <svg class="w-12 h-12 text-[#0f0f0f]" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                    </svg>
                </div>

                <h1 class="text-5xl md:text-6xl font-bold mb-6 text-white dark:text-white light:text-[#111827] uppercase" style="font-family: 'Unbounded', sans-serif;">
                    Thank You!
                </h1>
                <p class="text-lg md:text-xl text-[#676665] dark:text-[#676665] light:text-[#6b7280] mb-8" style="font-family: 'Urbanist', sans-serif;">
                    Your customization request has been successfully submitted. We're excited to bring your vision to life!
                </p>
            </div>
        </div>

        <!-- Content Section -->
        <div class="container mx-auto px-4 py-16">
            <div class="max-w-4xl mx-auto">
                <!-- Main Content Card -->
                <div class="bg-[#1a1a1a] dark:bg-[#1a1a1a] light:bg-white rounded-2xl p-8 border border-[#404040] dark:border-[#404040] light:border-[#d1d5db]">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-semibold text-white dark:text-white light:text-[#111827] mb-4" style="font-family: 'Unbounded', sans-serif;">
                            What Happens Next?
                        </h2>
                        <p class="text-[#676665] dark:text-[#676665] light:text-[#6b7280] text-lg" style="font-family: 'Urbanist', sans-serif;">
                            Here's what you can expect in the coming days:
                        </p>
                    </div>

                    <!-- Process Steps -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                        <!-- Step 1 -->
                        <div class="text-center">
                            <div class="w-16 h-16 bg-[#2a2a2a] dark:bg-[#2a2a2a] light:bg-[#f3f4f6] rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl font-bold text-[#c2b4a3]">1</span>
                            </div>
                            <h3 class="text-xl font-semibold text-white dark:text-white light:text-[#111827] mb-3" style="font-family: 'Unbounded', sans-serif;">
                                Review & Confirmation
                            </h3>
                            <p class="text-[#676665] dark:text-[#676665] light:text-[#6b7280] text-sm" style="font-family: 'Urbanist', sans-serif;">
                                Our team will review your request and send you a confirmation email within 24 hours.
                            </p>
                        </div>

                        <!-- Step 2 -->
                        <div class="text-center">
                            <div class="w-16 h-16 bg-[#2a2a2a] dark:bg-[#2a2a2a] light:bg-[#f3f4f6] rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl font-bold text-[#c2b4a3]">2</span>
                            </div>
                            <h3 class="text-xl font-semibold text-white dark:text-white light:text-[#111827] mb-3" style="font-family: 'Unbounded', sans-serif;">
                                Design & Quote
                            </h3>
                            <p class="text-[#676665] dark:text-[#676665] light:text-[#6b7280] text-sm" style="font-family: 'Urbanist', sans-serif;">
                                We'll create a detailed design proposal and provide you with a customized quote.
                            </p>
                        </div>

                        <!-- Step 3 -->
                        <div class="text-center">
                            <div class="w-16 h-16 bg-[#2a2a2a] dark:bg-[#2a2a2a] light:bg-[#f3f4f6] rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl font-bold text-[#c2b4a3]">3</span>
                            </div>
                            <h3 class="text-xl font-semibold text-white dark:text-white light:text-[#111827] mb-3" style="font-family: 'Unbounded', sans-serif;">
                                Production & Delivery
                            </h3>
                            <p class="text-[#676665] dark:text-[#676665] light:text-[#6b7280] text-sm" style="font-family: 'Urbanist', sans-serif;">
                                Once approved, we'll start production and keep you updated on the progress.
                            </p>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="bg-[#2a2a2a] dark:bg-[#2a2a2a] light:bg-[#f9fafb] rounded-xl p-6 mb-8">
                        <h3 class="text-xl font-semibold text-white dark:text-white light:text-[#111827] mb-4" style="font-family: 'Unbounded', sans-serif;">
                            Need to Make Changes?
                        </h3>
                        <p class="text-[#676665] dark:text-[#676665] light:text-[#6b7280] mb-4" style="font-family: 'Urbanist', sans-serif;">
                            If you need to modify your request or have any questions, please don't hesitate to contact us:
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-[#c2b4a3] mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                </svg>
                                <span class="text-white dark:text-white light:text-[#111827]" style="font-family: 'Urbanist', sans-serif;">
                                    maildikshantjoshi@gmail.com
                                </span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-[#c2b4a3] mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                                </svg>
                                <span class="text-white dark:text-white light:text-[#111827]" style="font-family: 'Urbanist', sans-serif;">
                                    +1 (555) 123-4567
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('shop.home.index') }}" 
                           class="bg-[#c2b4a3] hover:bg-[#ae9b84] text-[#0f0f0f] font-semibold py-3 px-6 rounded-lg transition-colors duration-200 text-center uppercase tracking-wide"
                           style="font-family: 'Urbanist', sans-serif;">
                            Continue Shopping
                        </a>
                        <a href="{{ route('shop.customisation.index') }}" 
                           class="bg-transparent border-2 border-[#c2b4a3] text-[#c2b4a3] hover:bg-[#c2b4a3] hover:text-[#0f0f0f] font-semibold py-3 px-6 rounded-lg transition-colors duration-200 text-center uppercase tracking-wide"
                           style="font-family: 'Urbanist', sans-serif;">
                            Submit Another Request
                        </a>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="mt-12 text-center">
                    <h3 class="text-2xl font-semibold text-white dark:text-white light:text-[#111827] mb-4" style="font-family: 'Unbounded', sans-serif;">
                        Why Choose Our Customization Service?
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Feature 1 -->
                        <div class="text-center">
                            <div class="w-12 h-12 bg-[#c2b4a3] rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-[#0f0f0f]" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-white dark:text-white light:text-[#111827] mb-2" style="font-family: 'Unbounded', sans-serif;">
                                Premium Quality
                            </h4>
                            <p class="text-[#676665] dark:text-[#676665] light:text-[#6b7280] text-sm" style="font-family: 'Urbanist', sans-serif;">
                                We use only the finest materials and craftsmanship for your custom pieces.
                            </p>
                        </div>

                        <!-- Feature 2 -->
                        <div class="text-center">
                            <div class="w-12 h-12 bg-[#c2b4a3] rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-[#0f0f0f]" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-white dark:text-white light:text-[#111827] mb-2" style="font-family: 'Unbounded', sans-serif;">
                                Expert Design
                            </h4>
                            <p class="text-[#676665] dark:text-[#676665] light:text-[#6b7280] text-sm" style="font-family: 'Urbanist', sans-serif;">
                                Our experienced designers will work closely with you to perfect your vision.
                            </p>
                        </div>

                        <!-- Feature 3 -->
                        <div class="text-center">
                            <div class="w-12 h-12 bg-[#c2b4a3] rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-[#0f0f0f]" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-white dark:text-white light:text-[#111827] mb-2" style="font-family: 'Unbounded', sans-serif;">
                                Timely Delivery
                            </h4>
                            <p class="text-[#676665] dark:text-[#676665] light:text-[#6b7280] text-sm" style="font-family: 'Urbanist', sans-serif;">
                                We respect your timeline and keep you updated throughout the production process.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-shop::layouts>
