@extends('shop::layouts.master')

@section('page_title')
    {{ __('Contact Us') }}
@endsection

@section('content-wrapper')
    <div class="bg-[#0f0f0f] dark:bg-[#0f0f0f] light:bg-white relative min-h-screen">
        <!-- Hero Section -->
        <div class="container mx-auto px-4 py-16">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-5xl md:text-6xl font-bold mb-6 text-white dark:text-white light:text-[#111827] uppercase" style="font-family: 'Unbounded', sans-serif;">
                    Your Partner in Every Step of Your Fashion Journey.
                </h1>
                <p class="text-lg md:text-xl text-[#676665] dark:text-[#676665] light:text-[#6b7280] mb-8" style="font-family: 'Urbanist', sans-serif;">
                    24/7 Assistance for Seamless Shopping and Unmatched Customer Satisfaction.
                </p>
            </div>
        </div>

        <!-- Contact Information Section -->
        <div class="container mx-auto px-4 py-16">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-semibold text-white dark:text-white light:text-[#111827] uppercase mb-4" style="font-family: 'Unbounded', sans-serif;">
                        Contact Information
                    </h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Email Card -->
                    <div class="bg-[#1a1a1a] dark:bg-[#1a1a1a] light:bg-white rounded-2xl p-8 text-center border border-[#404040] dark:border-[#404040] light:border-[#d1d5db]">
                        <div class="w-16 h-16 bg-[#1a1a1a] dark:bg-[#1a1a1a] light:bg-[#f3f4f6] rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8 text-white dark:text-white light:text-[#111827]" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white dark:text-white light:text-[#111827] mb-4" style="font-family: 'Unbounded', sans-serif;">
                            Email
                        </h3>
                        <div class="bg-[#1f1f1f] dark:bg-[#1f1f1f] light:bg-[#f9fafb] rounded-lg p-4">
                            <p class="text-white dark:text-white light:text-[#111827] text-sm" style="font-family: 'Urbanist', sans-serif;">
                                support@StyleLoom.com
                            </p>
                        </div>
                    </div>

                    <!-- Phone Card -->
                    <div class="bg-[#1a1a1a] dark:bg-[#1a1a1a] light:bg-white rounded-2xl p-8 text-center border border-[#404040] dark:border-[#404040] light:border-[#d1d5db]">
                        <div class="w-16 h-16 bg-[#1a1a1a] dark:bg-[#1a1a1a] light:bg-[#f3f4f6] rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8 text-white dark:text-white light:text-[#111827]" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white dark:text-white light:text-[#111827] mb-4" style="font-family: 'Unbounded', sans-serif;">
                            Phone
                        </h3>
                        <div class="bg-[#1f1f1f] dark:bg-[#1f1f1f] light:bg-[#f9fafb] rounded-lg p-4">
                            <p class="text-white dark:text-white light:text-[#111827] text-sm" style="font-family: 'Urbanist', sans-serif;">
                                +1 (555) 123-4567
                            </p>
                        </div>
                    </div>

                    <!-- Location Card -->
                    <div class="bg-[#1a1a1a] dark:bg-[#1a1a1a] light:bg-white rounded-2xl p-8 text-center border border-[#404040] dark:border-[#404040] light:border-[#d1d5db]">
                        <div class="w-16 h-16 bg-[#1a1a1a] dark:bg-[#1a1a1a] light:bg-[#f3f4f6] rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8 text-white dark:text-white light:text-[#111827]" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white dark:text-white light:text-[#111827] mb-4" style="font-family: 'Unbounded', sans-serif;">
                            Location
                        </h3>
                        <div class="bg-[#1f1f1f] dark:bg-[#1f1f1f] light:bg-[#f9fafb] rounded-lg p-4">
                            <p class="text-white dark:text-white light:text-[#111827] text-sm" style="font-family: 'Urbanist', sans-serif;">
                                Get Direction
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Testimonials Section -->
        <div class="container mx-auto px-4 py-16">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-4xl md:text-5xl font-bold text-white dark:text-white light:text-[#111827] uppercase mb-6" style="font-family: 'Unbounded', sans-serif;">
                        The StyleLoom Testimonial Collection.
                    </h2>
                    <p class="text-lg text-[#676665] dark:text-[#676665] light:text-[#6b7280] mb-8" style="font-family: 'Urbanist', sans-serif;">
                        At StyleLoom, our customers are the heartbeat of our brand.
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Testimonial 1 -->
                    <div class="bg-[#1a1a1a] dark:bg-[#1a1a1a] light:bg-white rounded-2xl p-8 border border-[#404040] dark:border-[#404040] light:border-[#d1d5db]">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-[#ae9b84] rounded-full mr-4"></div>
                            <div>
                                <h4 class="text-lg font-semibold text-white dark:text-white light:text-[#111827]" style="font-family: 'Unbounded', sans-serif;">
                                    Sarah Thompson
                                </h4>
                                <p class="text-[#676665] dark:text-[#676665] light:text-[#6b7280] text-sm" style="font-family: 'Urbanist', sans-serif;">
                                    New York, USA
                                </p>
                            </div>
                        </div>
                        <div class="flex mb-4">
                            <div class="flex text-yellow-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-[#81807e] dark:text-[#81807e] light:text-[#6b7280] text-base" style="font-family: 'Urbanist', sans-serif;">
                            StyleLoom exceeded my expectations. The gown's quality and design made me feel like a queen. Fast shipping, too!
                        </p>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="bg-[#1a1a1a] dark:bg-[#1a1a1a] light:bg-white rounded-2xl p-8 border border-[#404040] dark:border-[#404040] light:border-[#d1d5db]">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-[#ae9b84] rounded-full mr-4"></div>
                            <div>
                                <h4 class="text-lg font-semibold text-white dark:text-white light:text-[#111827]" style="font-family: 'Unbounded', sans-serif;">
                                    Rajesh Patel
                                </h4>
                                <p class="text-[#676665] dark:text-[#676665] light:text-[#6b7280] text-sm" style="font-family: 'Urbanist', sans-serif;">
                                    Mumbai, India
                                </p>
                            </div>
                        </div>
                        <div class="flex mb-4">
                            <div class="flex text-yellow-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-[#81807e] dark:text-[#81807e] light:text-[#6b7280] text-base" style="font-family: 'Urbanist', sans-serif;">
                            Absolutely love the style and warmth of the jacket. A perfect blend of fashion and functionality!
                        </p>
                    </div>

                    <!-- Testimonial 3 -->
                    <div class="bg-[#1a1a1a] dark:bg-[#1a1a1a] light:bg-white rounded-2xl p-8 border border-[#404040] dark:border-[#404040] light:border-[#d1d5db]">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-[#ae9b84] rounded-full mr-4"></div>
                            <div>
                                <h4 class="text-lg font-semibold text-white dark:text-white light:text-[#111827]" style="font-family: 'Unbounded', sans-serif;">
                                    Emily Walker
                                </h4>
                                <p class="text-[#676665] dark:text-[#676665] light:text-[#6b7280] text-sm" style="font-family: 'Urbanist', sans-serif;">
                                    London, UK
                                </p>
                            </div>
                        </div>
                        <div class="flex mb-4">
                            <div class="flex text-yellow-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-[#81807e] dark:text-[#81807e] light:text-[#6b7280] text-base" style="font-family: 'Urbanist', sans-serif;">
                            Adorable and comfortable! My daughter loves her new outfit. Thank you, StyleLoom, for dressing our little fashionista.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="container mx-auto px-4 py-16">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-4xl md:text-5xl font-bold text-white dark:text-white light:text-[#111827] uppercase mb-6" style="font-family: 'Unbounded', sans-serif;">
                        Have Questions? We Have Answers.
                    </h2>
                    <p class="text-lg text-[#676665] dark:text-[#676665] light:text-[#6b7280] mb-8" style="font-family: 'Urbanist', sans-serif;">
                        Ease into the world of StyleLoom with clarity. Our FAQs cover a spectrum of topics.
                    </p>
                </div>
                
                <!-- FAQ Tabs -->
                <div class="flex flex-wrap justify-center gap-4 mb-12">
                    <button class="px-6 py-3 rounded-lg border border-[#333333] dark:border-[#333333] light:border-[#d1d5db] text-[#b3b3b2] dark:text-[#b3b3b2] light:text-[#6b7280] hover:bg-[#c2b4a3] hover:text-[#0f0f0f] transition-colors" style="font-family: 'Urbanist', sans-serif;">
                        All
                    </button>
                    <button class="px-6 py-3 rounded-lg border border-[#333333] dark:border-[#333333] light:border-[#d1d5db] text-[#b3b3b2] dark:text-[#b3b3b2] light:text-[#6b7280] hover:bg-[#c2b4a3] hover:text-[#0f0f0f] transition-colors" style="font-family: 'Urbanist', sans-serif;">
                        Ordering
                    </button>
                    <button class="px-6 py-3 rounded-lg bg-[#c2b4a3] text-[#0f0f0f] border border-[#c2b4a3]" style="font-family: 'Urbanist', sans-serif;">
                        Shipping
                    </button>
                    <button class="px-6 py-3 rounded-lg border border-[#333333] dark:border-[#333333] light:border-[#d1d5db] text-[#b3b3b2] dark:text-[#b3b3b2] light:text-[#6b7280] hover:bg-[#c2b4a3] hover:text-[#0f0f0f] transition-colors" style="font-family: 'Urbanist', sans-serif;">
                        Returns
                    </button>
                    <button class="px-6 py-3 rounded-lg border border-[#333333] dark:border-[#333333] light:border-[#d1d5db] text-[#b3b3b2] dark:text-[#b3b3b2] light:text-[#6b7280] hover:bg-[#c2b4a3] hover:text-[#0f0f0f] transition-colors" style="font-family: 'Urbanist', sans-serif;">
                        Support
                    </button>
                </div>
                
                <!-- FAQ Content -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="space-y-8">
                        <div class="border-b border-[#404040] dark:border-[#404040] light:border-[#d1d5db] pb-6">
                            <h3 class="text-xl font-semibold text-white dark:text-white light:text-[#111827] mb-3" style="font-family: 'Unbounded', sans-serif;">
                                How can I place an order on StyleLoom?
                            </h3>
                            <p class="text-[#81807e] dark:text-[#81807e] light:text-[#6b7280] text-base" style="font-family: 'Urbanist', sans-serif;">
                                Ordering is easy! Simply browse our website, add items to your cart, and proceed to checkout. Follow the prompts to enter your details and complete your purchase.
                            </p>
                        </div>
                        
                        <div class="border-b border-[#404040] dark:border-[#404040] light:border-[#d1d5db] pb-6">
                            <h3 class="text-xl font-semibold text-white dark:text-white light:text-[#111827] mb-3" style="font-family: 'Unbounded', sans-serif;">
                                What payment methods do you accept?
                            </h3>
                            <p class="text-[#81807e] dark:text-[#81807e] light:text-[#6b7280] text-base" style="font-family: 'Urbanist', sans-serif;">
                                We accept a variety of payment methods, including credit/debit cards, net banking, and select digital wallets. Choose the option that suits you best during checkout.
                            </p>
                        </div>
                        
                        <div class="border-b border-[#404040] dark:border-[#404040] light:border-[#d1d5db] pb-6">
                            <h3 class="text-xl font-semibold text-white dark:text-white light:text-[#111827] mb-3" style="font-family: 'Unbounded', sans-serif;">
                                How can I track my order?
                            </h3>
                            <p class="text-[#81807e] dark:text-[#81807e] light:text-[#6b7280] text-base" style="font-family: 'Urbanist', sans-serif;">
                                Once your order is dispatched, you'll receive a tracking number via email. Use this number to track your package in real-time on our website.
                            </p>
                        </div>
                    </div>
                    
                    <div class="space-y-8">
                        <div class="border-b border-[#404040] dark:border-[#404040] light:border-[#d1d5db] pb-6">
                            <h3 class="text-xl font-semibold text-white dark:text-white light:text-[#111827] mb-3" style="font-family: 'Unbounded', sans-serif;">
                                Can I modify or cancel my order after placing it?
                            </h3>
                            <p class="text-[#81807e] dark:text-[#81807e] light:text-[#6b7280] text-base" style="font-family: 'Urbanist', sans-serif;">
                                Unfortunately, once an order is confirmed, modifications or cancellations may not be possible. Please review your order carefully before completing the purchase.
                            </p>
                        </div>
                        
                        <div class="border-b border-[#404040] dark:border-[#404040] light:border-[#d1d5db] pb-6">
                            <h3 class="text-xl font-semibold text-white dark:text-white light:text-[#111827] mb-3" style="font-family: 'Unbounded', sans-serif;">
                                How do I initiate a return?
                            </h3>
                            <p class="text-[#81807e] dark:text-[#81807e] light:text-[#6b7280] text-base" style="font-family: 'Urbanist', sans-serif;">
                                Visit our Returns page and follow the provided instructions. Ensure your item meets our return criteria, and our team will guide you through the process.
                            </p>
                        </div>
                        
                        <div class="border-b border-[#404040] dark:border-[#404040] light:border-[#d1d5db] pb-6">
                            <h3 class="text-xl font-semibold text-white dark:text-white light:text-[#111827] mb-3" style="font-family: 'Unbounded', sans-serif;">
                                Do you offer exchanges for products?
                            </h3>
                            <p class="text-[#81807e] dark:text-[#81807e] light:text-[#6b7280] text-base" style="font-family: 'Urbanist', sans-serif;">
                                At this time, we don't offer direct product exchanges. If you'd like a different item, please initiate a return and place a new order.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="container mx-auto px-4 py-16">
            <div class="max-w-6xl mx-auto">
                <div class="bg-[#c2b4a3] dark:bg-[#c2b4a3] light:bg-[#f3f4f6] rounded-2xl p-12 md:p-16">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                        <div>
                            <h2 class="text-4xl md:text-5xl font-bold text-[#0f0f0f] dark:text-[#0f0f0f] light:text-[#111827] uppercase mb-6" style="font-family: 'Unbounded', sans-serif;">
                                elevate your wardrobe
                            </h2>
                            <p class="text-lg text-[#1f1f1f] dark:text-[#1f1f1f] light:text-[#6b7280] mb-8" style="font-family: 'Urbanist', sans-serif;">
                                Don't miss out – experience the epitome of fashion by clicking 'Shop Now' and embrace a world of chic elegance delivered to your doorstep. Your style journey begins here.
                            </p>
                        </div>
                        <div class="text-center lg:text-right">
                            <a href="{{ route('shop.home.index') }}" class="inline-flex items-center bg-[#1f1f1f] dark:bg-[#1f1f1f] light:bg-[#111827] text-white px-6 py-4 rounded-lg hover:bg-[#333333] transition-colors" style="font-family: 'Urbanist', sans-serif;">
                                Shop Now
                                <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 