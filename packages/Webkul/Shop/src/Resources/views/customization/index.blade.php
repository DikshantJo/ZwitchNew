<x-shop::layouts>
    <div class="min-h-screen bg-gradient-to-br from-[#0f0f0f] via-[#1a1a1a] to-[#0f0f0f] py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-white mb-4" style="font-family: 'Unbounded', sans-serif;">
                    Customization Request
                </h1>
                <p class="text-[#676665] text-lg">
                    Tell us about your custom product requirements
                </p>
            </div>

            <!-- Form -->
            <form action="{{ route('shop.customisation.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                

                <!-- Display validation errors -->
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <h4 class="font-bold">Please fix the following errors:</h4>
                        <ul class="list-disc list-inside mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <!-- Display success message -->
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        {{ session('success') }}
                    </div>
                @endif
                
                <!-- Display error message -->
                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        {{ session('error') }}
                    </div>
                @endif
                
                <!-- Contact Information -->
                <div class="bg-[#1a1a1a] rounded-2xl p-8 border border-[#404040]">
                    <h2 class="text-2xl font-semibold text-white mb-6">Contact Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label class="block text-white mb-2">Full Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" 
                                   class="w-full px-4 py-3 bg-[#2a2a2a] border border-[#404040] text-white rounded-lg focus:ring-2 focus:ring-[#c2b4a3] focus:border-transparent" 
                                   placeholder="Enter your full name"
                                   required>
                            @error('name')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-white mb-2">Phone Number *</label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" 
                                   class="w-full px-4 py-3 bg-[#2a2a2a] border border-[#404040] text-white rounded-lg focus:ring-2 focus:ring-[#c2b4a3] focus:border-transparent" 
                                   placeholder="+91 98765 43210 or 9876543210"
                                   required>
                            @error('phone')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="md:col-span-2">
                            <label class="block text-white mb-2">Email Address *</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" 
                                   class="w-full px-4 py-3 bg-[#2a2a2a] border border-[#404040] text-white rounded-lg focus:ring-2 focus:ring-[#c2b4a3] focus:border-transparent" 
                                   placeholder="your.email@example.com"
                                   required>
                            @error('email')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Project Details -->
                <div class="bg-[#1a1a1a] rounded-2xl p-8 border border-[#404040]">
                    <h2 class="text-2xl font-semibold text-white mb-6">Project Details</h2>
                    
                    <div class="space-y-6">
                        <!-- Best Time to Contact -->
                        <div>
                            <label class="block text-white mb-2">Best Time to Contact *</label>
                            <select name="best_time_to_contact" id="best_time_to_contact" 
                                    class="w-full px-4 py-3 bg-[#2a2a2a] border border-[#404040] text-white rounded-lg focus:ring-2 focus:ring-[#c2b4a3] focus:border-transparent" 
                                    required>
                                <option value="">Select best time</option>
                                <option value="morning" {{ old('best_time_to_contact') == 'morning' ? 'selected' : '' }}>Morning (9 AM - 12 PM)</option>
                                <option value="afternoon" {{ old('best_time_to_contact') == 'afternoon' ? 'selected' : '' }}>Afternoon (12 PM - 5 PM)</option>
                                <option value="evening" {{ old('best_time_to_contact') == 'evening' ? 'selected' : '' }}>Evening (5 PM - 8 PM)</option>
                                <option value="anytime" {{ old('best_time_to_contact') == 'anytime' ? 'selected' : '' }}>Anytime</option>
                            </select>
                            @error('best_time_to_contact')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Preferred Contact Method -->
                        <div>
                            <label class="block text-white mb-2">Preferred Contact Method *</label>
                            <select name="preferred_contact" id="preferred_contact" 
                                    class="w-full px-4 py-3 bg-[#2a2a2a] border border-[#404040] text-white rounded-lg focus:ring-2 focus:ring-[#c2b4a3] focus:border-transparent" 
                                    required>
                                <option value="">Select preferred method</option>
                                <option value="phone" {{ old('preferred_contact') == 'phone' ? 'selected' : '' }}>Phone Call</option>
                                <option value="email" {{ old('preferred_contact') == 'email' ? 'selected' : '' }}>Email</option>
                                <option value="whatsapp" {{ old('preferred_contact') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                <option value="sms" {{ old('preferred_contact') == 'sms' ? 'selected' : '' }}>SMS</option>
                            </select>
                            @error('preferred_contact')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Customization Description -->
                        <div>
                            <label class="block text-white mb-2">Customization Description *</label>
                            <textarea name="customization_description" id="customization_description" rows="6" 
                                      class="w-full px-4 py-3 bg-[#2a2a2a] border border-[#404040] text-white rounded-lg focus:ring-2 focus:ring-[#c2b4a3] focus:border-transparent resize-none" 
                                      placeholder="Please describe your customization requirements in detail. Include specific details about colors, materials, dimensions, design preferences, and any other important specifications..." 
                                      required>{{ old('customization_description') }}</textarea>
                            <p class="text-[#676665] text-sm mt-2">Minimum 10 characters required</p>
                            @error('customization_description')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Files -->
                <div class="bg-[#1a1a1a] rounded-2xl p-8 border border-[#404040]">
                    <h2 class="text-2xl font-semibold text-white mb-6">Reference Files (Optional)</h2>
                    
                    <div>
                        <label class="block text-white mb-2">Upload Images or PDFs</label>
                        <input type="file" name="files[]" id="files" multiple 
                               accept="image/jpeg,image/jpg,image/png,application/pdf"
                               class="w-full px-4 py-3 bg-[#2a2a2a] border border-[#404040] text-white rounded-lg focus:ring-2 focus:ring-[#c2b4a3] focus:border-transparent">
                        <p class="text-[#676665] text-sm mt-2">JPEG, JPG, PNG, PDF (Max 5MB each)</p>
                        @error('files.*')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" id="submit-btn" 
                            class="bg-[#c2b4a3] text-[#0f0f0f] px-8 py-4 rounded-lg font-semibold text-lg hover:bg-[#b8a899] transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="submit-text">Submit Customization Request</span>
                        <span id="submit-loading" class="hidden">Submitting...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('customization-form');
            const submitBtn = document.getElementById('submit-btn');
            const submitText = document.getElementById('submit-text');
            const submitLoading = document.getElementById('submit-loading');
            
            // Phone number formatting for Indian numbers
            const phoneInput = document.getElementById('phone');
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                
                // Handle +91 prefix
                if (value.startsWith('91') && value.length > 2) {
                    value = '+91 ' + value.slice(2);
                } else if (value.startsWith('91') && value.length === 2) {
                    value = '+91 ';
                } else if (value.length > 0 && !value.startsWith('91')) {
                    // Format as Indian mobile number (10 digits)
                    if (value.length <= 5) {
                        value = value;
                    } else if (value.length <= 10) {
                        value = value.slice(0, 5) + ' ' + value.slice(5);
                    } else {
                        value = value.slice(0, 5) + ' ' + value.slice(5, 10);
                    }
                }
                
                e.target.value = value;
            });
            
            // Character counter for description
            const descriptionInput = document.getElementById('customization_description');
            const descriptionContainer = descriptionInput.parentElement;
            
            const counter = document.createElement('div');
            counter.className = 'text-[#676665] text-sm mt-1 text-right';
            counter.innerHTML = '<span id="char-count">0</span>/2000 characters';
            descriptionContainer.appendChild(counter);
            
            const charCount = document.getElementById('char-count');
            
            descriptionInput.addEventListener('input', function() {
                const length = this.value.length;
                charCount.textContent = length;
                
                if (length < 10) {
                    charCount.className = 'text-red-400';
                } else if (length > 1800) {
                    charCount.className = 'text-yellow-400';
                } else {
                    charCount.className = 'text-green-400';
                }
            });
            
            // Form submission handling
            form.addEventListener('submit', function(e) {
                // Basic client-side validation
                const name = document.getElementById('name').value.trim();
                const phone = document.getElementById('phone').value.trim();
                const email = document.getElementById('email').value.trim();
                const bestTime = document.getElementById('best_time_to_contact').value;
                const preferredContact = document.getElementById('preferred_contact').value;
                const description = document.getElementById('customization_description').value.trim();
                
                if (!name || !phone || !email || !bestTime || !preferredContact || !description) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                    return;
                }
                
                if (description.length < 10) {
                    e.preventDefault();
                    alert('Please provide a more detailed description (at least 10 characters).');
                    return;
                }
                
                // Show loading state
                submitBtn.disabled = true;
                submitText.classList.add('hidden');
                submitLoading.classList.remove('hidden');
            });
            
            // File upload validation
            const fileInput = document.getElementById('files');
            fileInput.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);
                const maxSize = 5 * 1024 * 1024; // 5MB
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                
                files.forEach((file, index) => {
                    if (file.size > maxSize) {
                        alert(`File "${file.name}" is too large. Maximum size is 5MB.`);
                        e.target.value = '';
                        return;
                    }
                    
                    if (!allowedTypes.includes(file.type)) {
                        alert(`File "${file.name}" is not a supported format. Please upload JPEG, JPG, PNG, or PDF files only.`);
                        e.target.value = '';
                        return;
                    }
                });
            });
        });
    </script>

</x-shop::layouts>
