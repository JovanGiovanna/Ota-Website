<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Hotel App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .register-bg {
            background: linear-gradient(135deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.3) 100%),
                        url('https://images.unsplash.com/photo-1571896349842-33c89424de2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2340&q=80');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">

<div class="flex min-h-screen">
    <!-- Left Side - Image Section -->
   <div class="hidden lg:flex lg:w-1/2 xl:w-3/5 register-bg relative">
        <div class="absolute inset-0 bg-gradient-to-r from-black/20 to-transparent"></div>
        <div class="relative z-10 flex flex-col justify-center px-12 text-white">
            <div class="max-w-md">
                <h1 class="text-4xl xl:text-5xl font-bold leading-tight mb-6">
                    Begin Your Journey of Luxury
                </h1>
                <p class="text-lg xl:text-xl leading-relaxed opacity-90">
                    Indulge In A Sanctuary Where Luxury Meets The Rhythm Of The Sea. Sign In To Access Your Exclusive Stay Curated Experience , And Unparalleled Service Tailored Just For You
                </p>
            </div>
        </div>
    </div>

    <!-- Right Side - Register Form -->
    <div class="w-full lg:w-1/2 xl:w-2/5 flex items-center justify-center p-6 lg:p-12">
        <div class="w-full max-w-md">
            <!-- Mobile Header (visible on small screens) -->
            <div class="lg:hidden text-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800 mb-3">
                    Create Account
                </h1>
                <p class="text-gray-600">
                    Join our luxury hospitality experience
                </p>
            </div>

            <!-- Error Messages -->
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg hidden" id="error-message">
                <!-- Error will be displayed here -->
            </div>

            <!-- Register Form -->
            <form method="POST" action="{{ route('register.web') }}" class="space-y-5">
                @csrf
                
                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Lengkap
                    </label>
                    <input 
                        id="name" 
                        type="text" 
                        name="name" 
                        value="{{ old('name') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-transparent transition duration-200 text-gray-900 placeholder-gray-500"
                        placeholder="Masukkan nama lengkap Anda"
                        required 
                        autofocus
                    >
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email
                    </label>
                    <input 
                        id="email" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-transparent transition duration-200 text-gray-900 placeholder-gray-500"
                        placeholder="Masukkan alamat email Anda"
                        required
                    >
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <input 
                        id="password" 
                        type="password" 
                        name="password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-transparent transition duration-200 text-gray-900 placeholder-gray-500"
                        placeholder="Minimal 8 karakter"
                        required
                    >
                    <div class="mt-2 text-xs text-gray-500">
                        Password harus minimal 8 karakter
                    </div>
                </div>

                <!-- Confirm Password Field -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Konfirmasi Password
                    </label>
                    <input 
                        id="password_confirmation" 
                        type="password" 
                        name="password_confirmation"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-transparent transition duration-200 text-gray-900 placeholder-gray-500"
                        placeholder="Ulangi password Anda"
                        required
                    >
                </div>

                <!-- Register Button -->
                <button 
                    type="submit"
                    class="w-full bg-gray-900 text-white py-3 px-4 rounded-lg hover:bg-gray-800 focus:ring-2 focus:ring-gray-800 focus:ring-offset-2 transition duration-200 font-medium text-lg"
                >
                    Create Account
                </button>
            </form>

            <!-- Login Link -->
            <p class="mt-6 text-center text-sm text-gray-600">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-gray-800 hover:text-gray-600 font-medium ml-1">
                    Sign in here
                </a>
            </p>

            <!-- Divider -->
            <div class="mt-8">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-gray-50 text-gray-500">Or register with</span>
                    </div>
                </div>
            </div>

            <!-- Social Register Options -->
            <div class="mt-6 grid grid-cols-2 gap-3">
                <button class="w-full inline-flex justify-center py-3 px-4 border border-gray-300 rounded-lg bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition duration-200">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span class="ml-2">Google</span>
                </button>
                <button class="w-full inline-flex justify-center py-3 px-4 border border-gray-300 rounded-lg bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition duration-200">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span class="ml-2">Facebook</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Handle form submission and validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const errorDiv = document.getElementById('error-message');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');
        
        // Show error message if there are Laravel errors
        @if ($errors->any())
            errorDiv.textContent = "{{ $errors->first() }}";
            errorDiv.classList.remove('hidden');
        @endif
        
        // Password strength indicator
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strengthIndicator = document.querySelector('.password-strength');
            
            if (password.length >= 8) {
                this.classList.remove('border-red-300');
                this.classList.add('border-green-300');
            } else if (password.length > 0) {
                this.classList.remove('border-green-300');
                this.classList.add('border-red-300');
            } else {
                this.classList.remove('border-red-300', 'border-green-300');
            }
        });
        
        // Real-time password confirmation
        confirmPasswordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirmPassword = this.value;
            
            if (confirmPassword === password && confirmPassword.length > 0) {
                this.classList.remove('border-red-300');
                this.classList.add('border-green-300');
            } else if (confirmPassword.length > 0) {
                this.classList.remove('border-green-300');
                this.classList.add('border-red-300');
            } else {
                this.classList.remove('border-red-300', 'border-green-300');
            }
        });
        
        // Form validation
        form.addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            const termsAccepted = document.getElementById('terms').checked;
            
            // Clear previous error
            errorDiv.classList.add('hidden');
            
            // Validate required fields
            if (!name || !email || !password || !confirmPassword) {
                e.preventDefault();
                showError('Mohon lengkapi semua field yang diperlukan.');
                return;
            }
            
            // Validate name length
            if (name.length < 2) {
                e.preventDefault();
                showError('Nama harus minimal 2 karakter.');
                return;
            }
            
            // Validate email format
            if (!isValidEmail(email)) {
                e.preventDefault();
                showError('Format email tidak valid.');
                return;
            }
            
            // Validate password length
            if (password.length < 8) {
                e.preventDefault();
                showError('Password harus minimal 8 karakter.');
                return;
            }
            
            // Validate password confirmation
            if (password !== confirmPassword) {
                e.preventDefault();
                showError('Konfirmasi password tidak cocok.');
                return;
            }
            
            // Validate terms acceptance
            if (!termsAccepted) {
                e.preventDefault();
                showError('Anda harus menyetujui Terms of Service dan Privacy Policy.');
                return;
            }
        });
        
        function showError(message) {
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
            errorDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
        
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
    });
</script>

</body>
</html>