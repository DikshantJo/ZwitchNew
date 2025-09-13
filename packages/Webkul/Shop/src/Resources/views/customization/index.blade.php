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
                            <label class="block text-white mb-2">Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" 
                                   class="w-full px-4 py-3 bg-[#2a2a2a] border border-[#404040] text-white rounded-lg focus:ring-2 focus:ring-[#c2b4a3] focus:border-transparent" 
                                   required>
                            @error('name')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-white mb-2">Phone *</label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" 
                                   class="w-full px-4 py-3 bg-[#2a2a2a] border border-[#404040] text-white rounded-lg focus:ring-2 focus:ring-[#c2b4a3] focus:border-transparent" 
                                   placeholder="+1234567890" required>
                            @error('phone')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="md:col-span-2">
                            <label class="block text-white mb-2">Email *</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" 
                                   class="w-full px-4 py-3 bg-[#2a2a2a] border border-[#404040] text-white rounded-lg focus:ring-2 focus:ring-[#c2b4a3] focus:border-transparent" 
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
                                      placeholder="Please describe your customization requirements in detail..." 
                                      required>{{ old('customization_description') }}</textarea>
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
                            class="bg-[#c2b4a3] text-[#0f0f0f] px-8 py-4 rounded-lg font-semibold text-lg hover:bg-[#b8a899] transition-colors">
                        Submit Customization Request
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-shop::layouts>
