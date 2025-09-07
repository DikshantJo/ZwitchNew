<!-- Page Layout -->
<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        Customisation Request
    </x-slot>

    <style>
        /* Ensure placeholder text is visible on dark backgrounds */
        input[type="tel"]::placeholder,
        input[type="text"]::placeholder,
        input[type="email"]::placeholder {
            color: #676665 !important;
            opacity: 1 !important;
        }
        
        /* Dark mode placeholder styling */
        .dark input[type="tel"]::placeholder,
        .dark input[type="text"]::placeholder,
        .dark input[type="email"]::placeholder {
            color: #676665 !important;
            opacity: 1 !important;
        }
        
        /* More specific targeting for phone input */
        input[name="phone"]::placeholder {
            color: #676665 !important;
            opacity: 1 !important;
        }
        
        /* Webkit browsers placeholder styling */
        input[name="phone"]::-webkit-input-placeholder {
            color: #676665 !important;
            opacity: 1 !important;
        }
        
        input[name="phone"]::-moz-placeholder {
            color: #676665 !important;
            opacity: 1 !important;
        }
        
        input[name="phone"]:-ms-input-placeholder {
            color: #676665 !important;
            opacity: 1 !important;
        }
        
        /* Target phone input by class */
        .phone-input::placeholder {
            color: #676665 !important;
            opacity: 1 !important;
        }
        
        .phone-input::-webkit-input-placeholder {
            color: #676665 !important;
            opacity: 1 !important;
        }
        
        .phone-input::-moz-placeholder {
            color: #676665 !important;
            opacity: 1 !important;
        }
        
        .phone-input:-ms-input-placeholder {
            color: #676665 !important;
            opacity: 1 !important;
        }
        
        /* Date input styling */
        input[type="date"] {
            color: white !important;
            background-color: #2a2a2a !important;
            cursor: pointer !important;
            position: relative;
        }
        input[type="date"]:hover {
            background-color: #333333 !important;
        }
        input[type="date"]:focus {
            background-color: #2a2a2a !important;
            outline: none;
        }
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            background: transparent;
        }
        input[type="date"]::-webkit-datetime-edit-text {
            color: white !important;
            cursor: pointer;
        }
        input[type="date"]::-webkit-datetime-edit-month-field,
        input[type="date"]::-webkit-datetime-edit-day-field,
        input[type="date"]::-webkit-datetime-edit-year-field {
            color: white !important;
            cursor: pointer;
        }
        /* Make the entire input area clickable */
        input[type="date"]::-webkit-datetime-edit {
            cursor: pointer;
            width: 100%;
            height: 100%;
        }
    </style>

    <div class="bg-[#0f0f0f] dark:bg-[#0f0f0f] light:bg-white relative min-h-screen">
        <!-- Hero Section -->
        <div class="container mx-auto px-4 py-16">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-5xl md:text-6xl font-bold mb-6 text-white dark:text-white light:text-[#111827] uppercase" style="font-family: 'Unbounded', sans-serif;">
                    Customize Your Perfect Piece
                </h1>
                <p class="text-lg md:text-xl text-[#676665] dark:text-[#676665] light:text-[#6b7280] mb-8" style="font-family: 'Urbanist', sans-serif;">
                    Tell us your vision and we'll bring it to life. Share your ideas, upload references, and let's create something extraordinary together.
                </p>
            </div>
        </div>

        <!-- Form Section -->
        <div class="container mx-auto px-4 py-16" style="padding-top:0px !important;">
            <div class="max-w-4xl mx-auto">
                <!-- Form Container -->
                <div class="bg-[#1a1a1a] dark:bg-[#1a1a1a] light:bg-white rounded-2xl p-8 md:p-12 border border-[#404040] dark:border-[#404040] light:border-[#d1d5db] shadow-2xl backdrop-blur-sm">
                    <x-shop::form :action="route('shop.customisation.submit')" enctype="multipart/form-data">
                        
                        <!-- Form Progress Indicator -->
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-white dark:text-white light:text-[#111827]" style="font-family: 'Urbanist', sans-serif;">
                                    Form Progress
                                </h3>
                                <span id="progress-percentage" class="text-sm text-[#c2b4a3] font-medium">0%</span>
                            </div>
                            <div class="w-full bg-[#2a2a2a] dark:bg-[#2a2a2a] light:bg-[#e5e7eb] rounded-full h-2">
                                <div id="form-progress-bar" class="bg-[#c2b4a3] h-2 rounded-full transition-all duration-500 ease-out" style="width: 0%"></div>
                            </div>
                        </div>
                        
                        <!-- Success/Error Messages -->
                        @if (session('success'))
                            <div class="mb-8 p-6 bg-green-100 border border-green-400 text-green-700 rounded-xl shadow-lg">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-medium">{{ session('success') }}</span>
                                </div>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mb-8 p-6 bg-red-100 border border-red-400 text-red-700 rounded-xl shadow-lg">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-medium">{{ session('error') }}</span>
                                </div>
                            </div>
                        @endif

                        <!-- Personal Information Section -->
                        <div class="mb-12">
                            <div class="flex items-center mb-8">
                                <div class="w-12 h-12 bg-[#c2b4a3] rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-[#0f0f0f]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <h2 class="text-2xl font-semibold text-white dark:text-white light:text-[#111827]" style="font-family: 'Unbounded', sans-serif;">
                                    Personal Information
                                </h2>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Name -->
                                <x-shop::form.control-group>
                                    <x-shop::form.control-group.label class="required text-white dark:text-white light:text-[#111827]">
                                        Full Name
                                    </x-shop::form.control-group.label>
                                    <x-shop::form.control-group.control
                                        type="text"
                                        class="px-4 py-3 bg-[#2a2a2a] dark:bg-[#2a2a2a] light:bg-[#f9fafb] border border-[#404040] dark:border-[#404040] light:border-[#d1d5db] text-white dark:text-white light:text-[#111827] rounded-lg focus:ring-2 focus:ring-[#c2b4a3] focus:border-transparent"
                                        name="name"
                                        rules="required|max:255"
                                        :value="old('name')"
                                        placeholder="Enter your full name"
                                        aria-required="true"
                                    />
                                    <x-shop::form.control-group.error control-name="name" />
                                </x-shop::form.control-group>

                                <!-- Phone -->
                                <x-shop::form.control-group>
                                    <x-shop::form.control-group.label class="required text-white dark:text-white light:text-[#111827]">
                                        Phone Number
                                    </x-shop::form.control-group.label>
                                    <input
                                        type="tel"
                                        class="phone-input px-4 py-3 bg-[#2a2a2a] dark:bg-[#2a2a2a] light:bg-[#f9fafb] border border-[#404040] dark:border-[#404040] light:border-[#d1d5db] text-white dark:text-white light:text-[#111827] rounded-lg focus:ring-2 focus:ring-[#c2b4a3] focus:border-transparent w-full"
                                        name="phone"
                                        value="{{ old('phone') }}"
                                        placeholder="+1234567890 or 1234567890"
                                        aria-required="true"
                                        style="color: white;"
                                        required
                                    />
                                    <x-shop::form.control-group.error control-name="phone" />
                                    <div class="mt-2 p-3 bg-[#2a2a2a]">
                                        <div class="flex items-center text-[#c2b4a3]">
                                            <svg class="w-4 h-4 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="text-sm font-medium">Format: Enter digits only (e.g., +1234567890 or 1234567890)</span>
                                        </div>
                                    </div>
                                </x-shop::form.control-group>
                            </div>

                            <!-- Email -->
                            <div class="mt-6">
                                <x-shop::form.control-group>
                                    <x-shop::form.control-group.label class="required text-white dark:text-white light:text-[#111827]">
                                        Email Address
                                    </x-shop::form.control-group.label>
                                    <x-shop::form.control-group.control
                                        type="email"
                                        class="px-4 py-3 bg-[#2a2a2a] dark:bg-[#2a2a2a] light:bg-[#f9fafb] border border-[#404040] dark:border-[#404040] light:border-[#d1d5db] text-white dark:text-white light:text-[#111827] rounded-lg focus:ring-2 focus:ring-[#c2b4a3] focus:border-transparent w-full"
                                        name="email"
                                        rules="required|email|max:255"
                                        :value="old('email')"
                                        placeholder="Enter your email address"
                                        aria-required="true"
                                    />
                                    <x-shop::form.control-group.error control-name="email" />
                                </x-shop::form.control-group>
                            </div>
                        </div>

                        <!-- Product Information Section -->
                        <div class="mb-12">
                            <div class="flex items-center mb-8">
                                <div class="w-12 h-12 bg-[#c2b4a3] rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-[#0f0f0f]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <h2 class="text-2xl font-semibold text-white dark:text-white light:text-[#111827]" style="font-family: 'Unbounded', sans-serif;">
                                    Product Information
                                </h2>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Category -->
                                <x-shop::form.control-group>
                                    <x-shop::form.control-group.label class="required text-white dark:text-white light:text-[#111827]">
                                        Category
                                    </x-shop::form.control-group.label>
                                    <select 
                                        name="category_id" 
                                        id="category_id"
                                        class="px-4 py-3 bg-[#2a2a2a] dark:bg-[#2a2a2a] light:bg-[#f9fafb] border border-[#404040] dark:border-[#404040] light:border-[#d1d5db] text-white dark:text-white light:text-[#111827] rounded-lg focus:ring-2 focus:ring-[#c2b4a3] focus:border-transparent w-full"
                                        required
                                    >
                                        <option value="">Select a category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-shop::form.control-group.error control-name="category_id" />
                                </x-shop::form.control-group>

                                <!-- Product -->
                                <x-shop::form.control-group>
                                    <x-shop::form.control-group.label class="required text-white dark:text-white light:text-[#111827]">
                                        Product
                                    </x-shop::form.control-group.label>
                                    <select 
                                        name="product_id" 
                                        id="product_id"
                                        class="px-4 py-3 bg-[#2a2a2a] dark:bg-[#2a2a2a] light:bg-[#f9fafb] border border-[#404040] dark:border-[#404040] light:border-[#d1d5db] text-white dark:text-white light:text-[#111827] rounded-lg focus:ring-2 focus:ring-[#c2b4a3] focus:border-transparent w-full"
                                        required
                                        disabled
                                    >
                                        <option value="">Select a product</option>
                                    </select>
                                    <div id="product-loading" class="hidden text-[#c2b4a3] text-sm mt-2">
                                        Loading products...
                                    </div>
                                    <x-shop::form.control-group.error control-name="product_id" />
                                </x-shop::form.control-group>
                            </div>

                            <!-- Quantity -->
                            <div class="mt-6">
                                <x-shop::form.control-group>
                                    <x-shop::form.control-group.label class="required text-white dark:text-white light:text-[#111827]">
                                        Quantity
                                    </x-shop::form.control-group.label>
                                    <x-shop::form.control-group.control
                                        type="number"
                                        class="px-4 py-3 bg-[#2a2a2a] dark:bg-[#2a2a2a] light:bg-[#f9fafb] border border-[#404040] dark:border-[#404040] light:border-[#d1d5db] text-white dark:text-white light:text-[#111827] rounded-lg focus:ring-2 focus:ring-[#c2b4a3] focus:border-transparent w-full max-w-xs"
                                        name="quantity"
                                        rules="required|integer|min:1"
                                        :value="old('quantity', 1)"
                                        min="1"
                                        placeholder="1"
                                        aria-required="true"
                                    />
                                    <x-shop::form.control-group.error control-name="quantity" />
                                </x-shop::form.control-group>
                            </div>
                        </div>

                        <!-- Project Details Section -->
                        <div class="mb-12">
                            <div class="flex items-center mb-8">
                                <div class="w-12 h-12 bg-[#c2b4a3] rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-[#0f0f0f]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h2 class="text-2xl font-semibold text-white dark:text-white light:text-[#111827]" style="font-family: 'Unbounded', sans-serif;">
                                    Project Details
                                </h2>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Budget -->
                                <x-shop::form.control-group>
                                    <x-shop::form.control-group.label class="required text-white dark:text-white light:text-[#111827]">
                                        Budget Range (in Rupees)
                                    </x-shop::form.control-group.label>
                                    <div class="relative">
                                        <select 
                                            name="budget" 
                                            id="budget"
                                            class="px-4 py-3 bg-[#2a2a2a] dark:bg-[#2a2a2a] light:bg-[#f9fafb] border border-[#404040] dark:border-[#404040] light:border-[#d1d5db] text-white dark:text-white light:text-[#111827] rounded-lg focus:ring-2 focus:ring-[#c2b4a3] focus:border-transparent w-full appearance-none cursor-pointer"
                                            required
                                        >
                                            <option value="">Select your budget range</option>
                                            @foreach($budgetOptions as $value => $label)
                                                <option value="{{ $value }}" {{ old('budget') == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <!-- Custom dropdown arrow -->
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <svg class="w-5 h-5 text-[#676665] dark:text-[#676665] light:text-[#6b7280]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <p class="text-sm text-[#676665] dark:text-[#676665] light:text-[#6b7280] mt-2">
                                        💡 <strong>Tip:</strong> Select a range that fits your budget. We'll work within your specified range to deliver the best value.
                                    </p>
                                    
                                    <!-- Budget Calculator -->
                                    <div id="budget-calculator" class="mt-4 p-4 bg-[#2a2a2a] dark:bg-[#2a2a2a] light:bg-[#f3f4f6] rounded-lg border border-[#404040] dark:border-[#404040] light:border-[#d1d5db] hidden">
                                        <h4 class="text-sm font-medium text-white dark:text-white light:text-[#111827] mb-2">What you can expect:</h4>
                                        <ul id="budget-features" class="text-xs text-[#676665] dark:text-[#676665] light:text-[#6b7280] space-y-1">
                                            <!-- Dynamic content will be inserted here -->
                                        </ul>
                                    </div>
                                    <x-shop::form.control-group.error control-name="budget" />
                                </x-shop::form.control-group>

                                <!-- Timeline -->
                                <div class="space-y-2">
                                    <label for="timeline" class="block text-white dark:text-white light:text-[#111827] font-medium mb-2">
                                        Timeline <span class="text-red-400">*</span>
                                    </label>
                                    <input 
                                        type="date" 
                                        id="timeline"
                                        name="timeline" 
                                        class="w-full px-4 py-3 bg-[#2a2a2a] dark:bg-[#2a2a2a] light:bg-[#f9fafb] border border-[#404040] dark:border-[#404040] light:border-[#d1d5db] text-white dark:text-white light:text-[#111827] rounded-lg focus:ring-2 focus:ring-[#c2b4a3] focus:border-transparent cursor-pointer"
                                        value="{{ old('timeline') }}"
                                        min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                        required
                                        aria-required="true"
                                        style="color: white; cursor: pointer;"
                                        onclick="this.showPicker && this.showPicker()"
                                    />
                                    <div id="timeline-error" class="text-red-400 text-sm mt-1 hidden"></div>
                                    <x-shop::form.control-group.error control-name="timeline" />
                                </div>
                            </div>
                        </div>

                        <!-- File Upload Section -->
                        <div class="mb-12">
                            <div class="flex items-center mb-8">
                                <div class="w-12 h-12 bg-[#c2b4a3] rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-[#0f0f0f]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                </div>
                                <h2 class="text-2xl font-semibold text-white dark:text-white light:text-[#111827]" style="font-family: 'Unbounded', sans-serif;">
                                    Reference Files (Optional)
                                </h2>
                            </div>
                            
                            <x-shop::form.control-group>
                                <x-shop::form.control-group.label class="text-white dark:text-white light:text-[#111827]">
                                    Upload Images or PDFs
                                </x-shop::form.control-group.label>
                                
                                <!-- Drag and Drop Zone -->
                                <div 
                                    id="drop-zone" 
                                    class="relative border-2 border-dashed border-[#404040] dark:border-[#404040] light:border-[#d1d5db] rounded-xl p-12 text-center transition-all duration-300 hover:border-[#c2b4a3] hover:bg-[#2a2a2a]/30 dark:hover:bg-[#2a2a2a]/30 light:hover:bg-[#f9fafb]/50 hover:shadow-lg hover:scale-[1.02]"
                                >
                                    <div id="drop-content">
                                        <svg class="mx-auto h-12 w-12 text-[#676665] dark:text-[#676665] light:text-[#6b7280] mb-4" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <p class="text-[#676665] dark:text-[#676665] light:text-[#6b7280] mb-2">
                                            <span class="font-medium text-[#c2b4a3]">Click to upload</span> or drag and drop
                                        </p>
                                        <p class="text-sm text-[#676665] dark:text-[#676665] light:text-[#6b7280]">
                                            JPEG, JPG, PNG, PDF (Max 5MB each)
                                        </p>
                                    </div>
                                    
                                    <!-- Hidden File Input -->
                                    <input 
                                        type="file" 
                                        name="files[]" 
                                        id="files"
                                        multiple
                                        accept="image/jpeg,image/jpg,image/png,application/pdf"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                    />
                                </div>
                                
                                <!-- File Preview Section -->
                                <div id="file-preview" class="mt-6 hidden">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-white dark:text-white light:text-[#111827] font-medium">Selected Files:</h4>
                                        <button 
                                            type="button" 
                                            id="clear-all-files"
                                            class="text-sm text-red-400 hover:text-red-300 underline"
                                        >
                                            Clear All
                                        </button>
                                    </div>
                                    <div id="file-list" class="space-y-3"></div>
                                </div>
                                
                                <!-- Upload Progress -->
                                <div id="upload-progress" class="mt-4 hidden">
                                    <div class="flex items-center justify-between text-sm text-[#676665] dark:text-[#676665] light:text-[#6b7280] mb-2">
                                        <span>Uploading files...</span>
                                        <span id="progress-text">0%</span>
                                    </div>
                                    <div class="w-full bg-[#2a2a2a] dark:bg-[#2a2a2a] light:bg-[#e5e7eb] rounded-full h-2">
                                        <div id="progress-bar" class="bg-[#c2b4a3] h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                    </div>
                                </div>
                                
                                <x-shop::form.control-group.error control-name="files.*" />
                            </x-shop::form.control-group>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center space-y-6 pt-8 border-t border-[#404040] dark:border-[#404040] light:border-[#d1d5db]">
                            <div class="flex justify-center mt-8">
                                <button 
                                    type="submit"
                                    id="submit-btn"
                                    class="bg-[#c2b4a3] hover:bg-[#ae9b84] px-8 text-[#0f0f0f] font-semibold py-4 px-12 rounded-xl transition-all duration-300 uppercase tracking-wide disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl hover:scale-105 transform"
                                    style="font-family: 'Urbanist', sans-serif;"
                                >
                                    <span class="flex items-center justify-center">
                                        <!-- <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg> -->
                                        Submit Customisation Request
                                    </span>
                                </button>
                            </div>
                            
                        </div>
                    </x-shop::form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for AJAX and File Preview -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('category_id');
            const productSelect = document.getElementById('product_id');
            const productLoading = document.getElementById('product-loading');
            const fileInput = document.getElementById('files');
            const filePreview = document.getElementById('file-preview');
            const fileList = document.getElementById('file-list');

            // Category change handler with enhanced loading and error handling
            categorySelect.addEventListener('change', function() {
                const categoryId = this.value;
                
                if (categoryId) {
                    // Show loading state
                    productLoading.classList.remove('hidden');
                    productSelect.disabled = true;
                    productSelect.innerHTML = '<option value="">Loading products...</option>';
                    
                    // Add loading spinner to the loading text
                    productLoading.innerHTML = `
                        <div class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-[#c2b4a3]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Loading products...
                        </div>
                    `;
                    
                    // Fetch products with timeout
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 second timeout
                    
                    fetch(`/customisation/products/${categoryId}`, {
                        signal: controller.signal,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    })
                        .then(response => {
                            clearTimeout(timeoutId);
                            
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            productLoading.classList.add('hidden');
                            
                            if (data.success && data.products.length > 0) {
                                productSelect.innerHTML = '<option value="">Select a product</option>';
                                data.products.forEach(product => {
                                    const option = document.createElement('option');
                                    option.value = product.id;
                                    option.textContent = product.name;
                                    if (product.sku) {
                                        option.textContent += ` (${product.sku})`;
                                    }
                                    productSelect.appendChild(option);
                                });
                                productSelect.disabled = false;
                                
                                // Show success message
                                showMessage(`Found ${data.products.length} products in ${data.category_name}`, 'success');
                            } else {
                                productSelect.innerHTML = '<option value="">No products found</option>';
                                productSelect.disabled = true;
                                showMessage('No products found in this category', 'warning');
                            }
                        })
                        .catch(error => {
                            clearTimeout(timeoutId);
                            productLoading.classList.add('hidden');
                            
                            if (error.name === 'AbortError') {
                                productSelect.innerHTML = '<option value="">Request timeout</option>';
                                showMessage('Request timed out. Please try again.', 'error');
                            } else {
                                productSelect.innerHTML = '<option value="">Error loading products</option>';
                                showMessage('Failed to load products. Please try again.', 'error');
                            }
                            productSelect.disabled = true;
                            console.error('Error loading products:', error);
                        });
                } else {
                    productSelect.innerHTML = '<option value="">Select a product</option>';
                    productSelect.disabled = true;
                    productLoading.classList.add('hidden');
                }
            });

            // Enhanced file upload functionality with drag and drop
            const dropZone = document.getElementById('drop-zone');
            const dropContent = document.getElementById('drop-content');
            const clearAllBtn = document.getElementById('clear-all-files');
            const uploadProgress = document.getElementById('upload-progress');
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');

            // File validation constants
            const maxSize = 5 * 1024 * 1024; // 5MB
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
            const maxFiles = 10; // Maximum number of files

            // Drag and drop event handlers
            dropZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                dropZone.classList.add('border-[#c2b4a3]', 'bg-[#2a2a2a]/50', 'dark:bg-[#2a2a2a]/50', 'light:bg-[#f9fafb]/50');
            });

            dropZone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                dropZone.classList.remove('border-[#c2b4a3]', 'bg-[#2a2a2a]/50', 'dark:bg-[#2a2a2a]/50', 'light:bg-[#f9fafb]/50');
            });

            dropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                dropZone.classList.remove('border-[#c2b4a3]', 'bg-[#2a2a2a]/50', 'dark:bg-[#2a2a2a]/50', 'light:bg-[#f9fafb]/50');
                
                const files = Array.from(e.dataTransfer.files);
                handleFiles(files);
            });

            // File input change handler
            fileInput.addEventListener('change', function() {
                const files = Array.from(this.files);
                handleFiles(files);
            });

            // Clear all files handler
            clearAllBtn.addEventListener('click', function() {
                fileInput.value = '';
                filePreview.classList.add('hidden');
                uploadProgress.classList.add('hidden');
                showMessage('All files cleared', 'info');
            });

            // Handle file processing
            function handleFiles(files) {
                if (files.length === 0) {
                    filePreview.classList.add('hidden');
                    return;
                }

                // Check file count limit
                if (files.length > maxFiles) {
                    showMessage(`Maximum ${maxFiles} files allowed. Please select fewer files.`, 'warning');
                    return;
                }

                // Validate and process files
                const validFiles = [];
                const invalidFiles = [];
                
                files.forEach(file => {
                    if (file.size > maxSize) {
                        invalidFiles.push({ file, error: 'File too large (max 5MB)' });
                    } else if (!allowedTypes.includes(file.type)) {
                        invalidFiles.push({ file, error: 'Invalid file type' });
                    } else {
                        validFiles.push(file);
                    }
                });

                // Show validation results
                if (invalidFiles.length > 0) {
                    const errorMessages = invalidFiles.map(item => `${item.file.name}: ${item.error}`).join(', ');
                    showMessage(`Some files were rejected: ${errorMessages}`, 'warning');
                }

                if (validFiles.length > 0) {
                    // Update file input with valid files
                    const dt = new DataTransfer();
                    validFiles.forEach(file => dt.items.add(file));
                    fileInput.files = dt.files;
                    
                    // Display file preview
                    displayFilePreview(validFiles);
                    filePreview.classList.remove('hidden');
                    
                    showMessage(`Successfully added ${validFiles.length} file(s)`, 'success');
                }
            }

            // Display file preview with enhanced UI
            function displayFilePreview(files) {
                fileList.innerHTML = '';
                
                files.forEach((file, index) => {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'flex items-center justify-between p-4 bg-[#2a2a2a] dark:bg-[#2a2a2a] light:bg-[#f3f4f6] rounded-lg border border-[#404040] dark:border-[#404040] light:border-[#d1d5db]';
                    
                    const fileInfo = document.createElement('div');
                    fileInfo.className = 'flex items-center space-x-3';
                    
                    // File icon based on type
                    const icon = getFileIcon(file.type);
                    fileInfo.innerHTML = `
                        <div class="flex-shrink-0">
                            ${icon}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white dark:text-white light:text-[#111827]">${file.name}</p>
                            <p class="text-xs text-[#676665] dark:text-[#676665] light:text-[#6b7280]">${formatFileSize(file.size)}</p>
                        </div>
                    `;
                    
                    // Remove button
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'flex-shrink-0 p-1 text-red-400 hover:text-red-300 hover:bg-red-400/10 rounded-full transition-colors';
                    removeBtn.innerHTML = `
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    `;
                    removeBtn.onclick = () => removeFile(index);
                    
                    fileItem.appendChild(fileInfo);
                    fileItem.appendChild(removeBtn);
                    fileList.appendChild(fileItem);
                });
            }

            // Get file icon based on type
            function getFileIcon(type) {
                if (type.startsWith('image/')) {
                    return `
                        <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    `;
                } else if (type === 'application/pdf') {
                    return `
                        <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    `;
                } else {
                    return `
                        <svg class="w-8 h-8 text-[#676665] dark:text-[#676665] light:text-[#6b7280]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    `;
                }
            }

            // Format file size
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Remove individual file
            function removeFile(index) {
                const dt = new DataTransfer();
                Array.from(fileInput.files).forEach((file, i) => {
                    if (i !== index) dt.items.add(file);
                });
                fileInput.files = dt.files;
                
                if (fileInput.files.length === 0) {
                    filePreview.classList.add('hidden');
                } else {
                    displayFilePreview(Array.from(fileInput.files));
                }
                
                showMessage('File removed', 'info');
            }

            // Form validation and submission
            const form = document.querySelector('form');
            const submitBtn = document.getElementById('submit-btn');
            
            // Form validation rules
            const validationRules = {
                name: {
                    required: true,
                    minLength: 2,
                    maxLength: 255,
                    pattern: /^[a-zA-Z\s]+$/,
                    message: 'Name must be 2-255 characters and contain only letters and spaces'
                },
                phone: {
                    required: true,
                    pattern: /^[\+]?[1-9][\d]{0,15}$/,
                    message: 'Please enter a valid phone number'
                },
                email: {
                    required: true,
                    pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                    message: 'Please enter a valid email address'
                },
                category_id: {
                    required: true,
                    message: 'Please select a category'
                },
                product_id: {
                    required: true,
                    message: 'Please select a product'
                },
                quantity: {
                    required: true,
                    min: 1,
                    pattern: /^\d+$/,
                    message: 'Quantity must be a positive number'
                },
                budget: {
                    required: true,
                    message: 'Please select a budget range'
                },
                timeline: {
                    required: true,
                    futureDate: true,
                    message: 'Please select a future date'
                }
            };

            // Real-time validation for all form fields
            function setupFieldValidation() {
                const fields = ['name', 'phone', 'email', 'category_id', 'product_id', 'quantity', 'budget', 'timeline'];
                
                fields.forEach(fieldName => {
                    const field = document.getElementById(fieldName);
                    if (field) {
                        // Validate on blur and input
                        field.addEventListener('blur', () => validateField(fieldName));
                        field.addEventListener('input', () => {
                            // Clear error on input for better UX
                            clearFieldError(fieldName);
                        });
                    }
                });
            }

            // Validate individual field
            function validateField(fieldName) {
                const field = document.getElementById(fieldName);
                const value = field.value.trim();
                const rules = validationRules[fieldName];
                
                if (!rules) return true;

                // Required validation
                if (rules.required && !value) {
                    showFieldError(fieldName, rules.message);
                    return false;
                }

                // Pattern validation
                if (value && rules.pattern && !rules.pattern.test(value)) {
                    showFieldError(fieldName, rules.message);
                    return false;
                }

                // Length validation
                if (value && rules.minLength && value.length < rules.minLength) {
                    showFieldError(fieldName, rules.message);
                    return false;
                }

                if (value && rules.maxLength && value.length > rules.maxLength) {
                    showFieldError(fieldName, rules.message);
                    return false;
                }

                // Number validation
                if (value && rules.min && parseInt(value) < rules.min) {
                    showFieldError(fieldName, rules.message);
                    return false;
                }

                // Future date validation
                if (value && rules.futureDate && fieldName === 'timeline') {
                    const selectedDate = new Date(value);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    
                    if (selectedDate <= today) {
                        showFieldError(fieldName, 'Timeline must be a future date');
                        return false;
                    }
                }

                // Clear error if validation passes
                clearFieldError(fieldName);
                return true;
            }

            // Show field error
            function showFieldError(fieldName, message) {
                const field = document.getElementById(fieldName);
                const errorElement = document.getElementById(`${fieldName}-error`);
                
                // Add error styling to field
                field.classList.add('border-red-500', 'focus:ring-red-500');
                field.classList.remove('border-[#404040]', 'focus:ring-[#c2b4a3]');
                
                // Show error message
                if (errorElement) {
                    errorElement.textContent = message;
                    errorElement.classList.remove('hidden');
                } else {
                    // Create error element if it doesn't exist
                    const errorDiv = document.createElement('div');
                    errorDiv.id = `${fieldName}-error`;
                    errorDiv.className = 'text-red-400 text-sm mt-1';
                    errorDiv.textContent = message;
                    field.parentNode.appendChild(errorDiv);
                }
            }

            // Clear field error
            function clearFieldError(fieldName) {
                const field = document.getElementById(fieldName);
                const errorElement = document.getElementById(`${fieldName}-error`);
                
                // Remove error styling from field
                field.classList.remove('border-red-500', 'focus:ring-red-500');
                field.classList.add('border-[#404040]', 'focus:ring-[#c2b4a3]');
                
                // Hide error message
                if (errorElement) {
                    errorElement.classList.add('hidden');
                }
            }

            // Validate entire form
            function validateForm() {
                let isValid = true;
                const fields = ['name', 'phone', 'email', 'category_id', 'product_id', 'quantity', 'budget', 'timeline'];
                
                fields.forEach(fieldName => {
                    if (!validateField(fieldName)) {
                        isValid = false;
                    }
                });

                // Additional validation for file uploads
                const files = Array.from(fileInput.files);
                if (files.length > 0) {
                    const invalidFiles = files.filter(file => 
                        file.size > maxSize || !allowedTypes.includes(file.type)
                    );
                    
                    if (invalidFiles.length > 0) {
                        showMessage('Please remove invalid files before submitting', 'error');
                        isValid = false;
                    }
                }

                return isValid;
            }

            // Form submission with validation
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validate form before submission
                if (!validateForm()) {
                    showMessage('Please fix the errors above before submitting', 'error');
                    return;
                }

                // Disable submit button to prevent double submission
                submitBtn.disabled = true;
                submitBtn.textContent = 'Submitting...';
                
                const files = Array.from(fileInput.files);
                if (files.length > 0) {
                    // Show upload progress
                    uploadProgress.classList.remove('hidden');
                    progressBar.style.width = '0%';
                    progressText.textContent = '0%';
                    
                    // Simulate progress (since we can't track actual upload progress with standard form submission)
                    let progress = 0;
                    const progressInterval = setInterval(() => {
                        progress += Math.random() * 15;
                        if (progress > 90) progress = 90;
                        
                        progressBar.style.width = progress + '%';
                        progressText.textContent = Math.round(progress) + '%';
                        
                        if (progress >= 90) {
                            clearInterval(progressInterval);
                        }
                    }, 200);
                }

                // Submit form
                form.submit();
            });

            // Initialize field validation
            setupFieldValidation();

            // Form state management
            let formState = {
                isValid: false,
                isSubmitting: false,
                hasErrors: false
            };

            // Update form state
            function updateFormState() {
                const fields = ['name', 'phone', 'email', 'category_id', 'product_id', 'quantity', 'budget', 'timeline'];
                let hasErrors = false;
                
                fields.forEach(fieldName => {
                    const field = document.getElementById(fieldName);
                    const errorElement = document.getElementById(`${fieldName}-error`);
                    
                    if (field && (!field.value.trim() || (errorElement && !errorElement.classList.contains('hidden')))) {
                        hasErrors = true;
                    }
                });

                formState.hasErrors = hasErrors;
                formState.isValid = !hasErrors && !formState.isSubmitting;
                
                // Update submit button state
                updateSubmitButton();
            }

            // Update submit button state
            function updateSubmitButton() {
                if (formState.isSubmitting) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Submitting...';
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else if (formState.hasErrors) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Please fix errors above';
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit Customization Request';
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }

            // Enhanced field validation with state management
            function validateFieldWithState(fieldName) {
                const isValid = validateField(fieldName);
                updateFormState();
                return isValid;
            }

            // Override existing validation functions to include state management
            const originalValidateField = validateField;
            validateField = function(fieldName) {
                const result = originalValidateField(fieldName);
                updateFormState();
                return result;
            };

            // Add real-time form state monitoring
            function setupFormStateMonitoring() {
                const fields = ['name', 'phone', 'email', 'category_id', 'product_id', 'quantity', 'budget', 'timeline'];
                
                fields.forEach(fieldName => {
                    const field = document.getElementById(fieldName);
                    if (field) {
                        field.addEventListener('input', updateFormState);
                        field.addEventListener('change', updateFormState);
                        field.addEventListener('blur', updateFormState);
                    }
                });

                // Monitor file uploads
                fileInput.addEventListener('change', updateFormState);
            }

            // Initialize form state monitoring
            setupFormStateMonitoring();

            // Form progress tracking
            function updateFormProgress() {
                const fields = ['name', 'phone', 'email', 'category_id', 'product_id', 'quantity', 'budget', 'timeline'];
                let completedFields = 0;
                
                fields.forEach(fieldName => {
                    const field = document.getElementById(fieldName);
                    if (field && field.value.trim()) {
                        completedFields++;
                    }
                });
                
                const progress = Math.round((completedFields / fields.length) * 100);
                const progressBar = document.getElementById('form-progress-bar');
                const progressPercentage = document.getElementById('progress-percentage');
                
                if (progressBar && progressPercentage) {
                    progressBar.style.width = progress + '%';
                    progressPercentage.textContent = progress + '%';
                }
            }

            // Add progress tracking to all form fields
            function setupProgressTracking() {
                const fields = ['name', 'phone', 'email', 'category_id', 'product_id', 'quantity', 'budget', 'timeline'];
                
                fields.forEach(fieldName => {
                    const field = document.getElementById(fieldName);
                    if (field) {
                        field.addEventListener('input', updateFormProgress);
                        field.addEventListener('change', updateFormProgress);
                    }
                });
            }

            // Initialize progress tracking
            setupProgressTracking();

            // Enhanced budget dropdown functionality
            function setupBudgetDropdown() {
                const budgetSelect = document.getElementById('budget');
                const budgetTip = budgetSelect.parentNode.querySelector('p');
                const budgetCalculator = document.getElementById('budget-calculator');
                const budgetFeatures = document.getElementById('budget-features');
                
                // Budget range descriptions
                const budgetDescriptions = {
                    'under_1000': 'Perfect for simple customizations and basic designs.',
                    '1000_2500': 'Great for standard customizations with moderate complexity.',
                    '2500_5000': 'Ideal for detailed customizations with premium materials.',
                    '5000_10000': 'Premium customizations with high-quality materials and complex designs.',
                    '10000_15000': 'Luxury customizations with premium materials and intricate details.',
                    '15000_25000': 'High-end customizations with premium materials and expert craftsmanship.',
                    '25000_50000': 'Premium luxury customizations with exclusive materials and designs.',
                    '50000_100000': 'Ultra-premium customizations with luxury materials and bespoke designs.',
                    '100000_plus': 'Exclusive luxury customizations with the finest materials and craftsmanship.',
                    'custom': 'Have a specific budget in mind? Let us know in your message and we\'ll work with you.'
                };

                // Budget features for each range
                const budgetFeaturesList = {
                    'under_1000': [
                        'Basic customization options',
                        'Standard materials',
                        'Simple design modifications',
                        'Standard delivery timeline'
                    ],
                    '1000_2500': [
                        'Enhanced customization options',
                        'Quality materials',
                        'Moderate design complexity',
                        'Standard delivery timeline'
                    ],
                    '2500_5000': [
                        'Premium customization options',
                        'High-quality materials',
                        'Complex design elements',
                        'Priority processing'
                    ],
                    '5000_10000': [
                        'Luxury customization options',
                        'Premium materials',
                        'Intricate design details',
                        'Fast-track delivery'
                    ],
                    '10000_15000': [
                        'Bespoke customization',
                        'Luxury materials',
                        'Expert craftsmanship',
                        'Express delivery'
                    ],
                    '15000_25000': [
                        'Exclusive customization',
                        'Premium luxury materials',
                        'Master craftsmanship',
                        'White-glove service'
                    ],
                    '25000_50000': [
                        'Ultra-premium customization',
                        'Exclusive materials',
                        'Artisan craftsmanship',
                        'Concierge service'
                    ],
                    '50000_100000': [
                        'Bespoke luxury customization',
                        'Rare and exclusive materials',
                        'Master artisan work',
                        'Personal consultation'
                    ],
                    '100000_plus': [
                        'Ultimate luxury customization',
                        'Finest materials available',
                        'World-class craftsmanship',
                        'VIP service experience'
                    ],
                    'custom': [
                        'Tailored to your specific needs',
                        'Flexible material options',
                        'Custom timeline',
                        'Personal consultation'
                    ]
                };

                // Update budget description and features on change
                budgetSelect.addEventListener('change', function() {
                    const selectedValue = this.value;
                    const description = budgetDescriptions[selectedValue];
                    const features = budgetFeaturesList[selectedValue];
                    
                    // Update description
                    if (description && budgetTip) {
                        budgetTip.innerHTML = `💡 <strong>Tip:</strong> ${description}`;
                    } else if (budgetTip) {
                        budgetTip.innerHTML = `💡 <strong>Tip:</strong> Select a range that fits your budget. We'll work within your specified range to deliver the best value.`;
                    }

                    // Update features list
                    if (features && budgetCalculator && budgetFeatures) {
                        budgetFeatures.innerHTML = '';
                        features.forEach(feature => {
                            const li = document.createElement('li');
                            li.className = 'flex items-center';
                            li.innerHTML = `
                                <svg class="w-3 h-3 mr-2 text-[#c2b4a3] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                ${feature}
                            `;
                            budgetFeatures.appendChild(li);
                        });
                        budgetCalculator.classList.remove('hidden');
                    } else if (budgetCalculator) {
                        budgetCalculator.classList.add('hidden');
                    }
                });

                // Add budget range indicators
                const budgetOptions = budgetSelect.querySelectorAll('option');
                budgetOptions.forEach(option => {
                    if (option.value) {
                        // Add visual indicators for different budget ranges
                        if (option.value.includes('under_1000') || option.value.includes('1000_2500')) {
                            option.textContent = `💰 ${option.textContent} - Basic`;
                        } else if (option.value.includes('2500_5000') || option.value.includes('5000_10000')) {
                            option.textContent = `💎 ${option.textContent} - Standard`;
                        } else if (option.value.includes('10000_15000') || option.value.includes('15000_25000')) {
                            option.textContent = `👑 ${option.textContent} - Premium`;
                        } else if (option.value.includes('25000_50000') || option.value.includes('50000_100000')) {
                            option.textContent = `💎 ${option.textContent} - Luxury`;
                        } else if (option.value.includes('100000_plus')) {
                            option.textContent = `🏆 ${option.textContent} - Ultra-Premium`;
                        } else if (option.value === 'custom') {
                            option.textContent = `✏️ ${option.textContent}`;
                        }
                    }
                });
            }

            // Initialize budget dropdown
            setupBudgetDropdown();



            // Message display function
            function showMessage(message, type = 'info') {
                // Remove existing messages
                const existingMessages = document.querySelectorAll('.ajax-message');
                existingMessages.forEach(msg => msg.remove());
                
                const messageDiv = document.createElement('div');
                messageDiv.className = `ajax-message p-3 rounded-lg mb-4 text-sm`;
                
                switch (type) {
                    case 'success':
                        messageDiv.className += ' bg-green-100 border border-green-400 text-green-700';
                        break;
                    case 'warning':
                        messageDiv.className += ' bg-yellow-100 border border-yellow-400 text-yellow-700';
                        break;
                    case 'error':
                        messageDiv.className += ' bg-red-100 border border-red-400 text-red-700';
                        break;
                    default:
                        messageDiv.className += ' bg-blue-100 border border-blue-400 text-blue-700';
                }
                
                messageDiv.textContent = message;
                
                // Insert after the product select
                productSelect.parentNode.insertBefore(messageDiv, productSelect.parentNode.children[productSelect.parentNode.children.length - 1]);
                
                // Auto-remove after 5 seconds
                setTimeout(() => {
                    if (messageDiv.parentNode) {
                        messageDiv.remove();
                    }
                }, 5000);
            }
        });
    </script>
</x-shop::layouts>
