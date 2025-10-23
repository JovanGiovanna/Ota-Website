<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Hotel App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .login-bg {
            background: linear-gradient(135deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.3) 100%),
                        url('https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2340&q=80');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">

<div class="flex min-h-screen">
    <!-- Left Side - Image Section -->
    <div class="hidden lg:flex lg:w-1/2 xl:w-3/5 login-bg relative">
        <div class="absolute inset-0 bg-gradient-to-r from-black/20 to-transparent"></div>
        <div class="relative z-10 flex flex-col justify-center px-12 text-white">
            <div class="max-w-md">
                <h1 class="text-4xl xl:text-5xl font-bold leading-tight mb-6">
                    Welcome To A World of Timeless Elegance
                </h1>
                <p class="text-lg xl:text-xl leading-relaxed opacity-90">
                    Indulge In A Sanctuary Where Luxury Meets The Rhythm Of The Sea. Sign In To Access Your Exclusive Stay Curated Experience , And Unparalleled Service Tailored Just For You
                </p>
            </div>
        </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="w-full lg:w-1/2 xl:w-2/5 flex items-center justify-center p-8 lg:p-12">
        <div class="w-full max-w-md">
            <!-- Mobile Header (visible on small screens) -->
            <div class="lg:hidden text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-3">
                    Welcome Back
                </h1>
                <p class="text-gray-600">
                    Sign in to your luxury sanctuary
                </p>
            </div>

            <!-- Error Messages -->
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg hidden" id="error-message">
                <!-- Error will be displayed here -->
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ request()->is('admin/login') ? route('admin.login.web') : (request()->is('vendor/login') ? route('vendor.login.web') : (request()->is('super-admin/login') ? route('super_admin.login.web') : route('login.web'))) }}" class="space-y-6">
                @csrf
                
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
                        placeholder="Enter your email address"
                        required 
                        autofocus
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
                        placeholder="Enter your password"
                        required
                    >
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input 
                            id="remember" 
                            name="remember" 
                            type="checkbox" 
                            class="h-4 w-4 text-gray-800 focus:ring-gray-800 border-gray-300 rounded"
                        >
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>
                    <a href="#" class="text-sm text-gray-800 hover:text-gray-600 font-medium">
                        Forgot password?
                    </a>
                </div>

                <!-- Login Button -->
                <button 
                    type="submit"
                    class="w-full bg-gray-900 text-white py-3 px-4 rounded-lg hover:bg-gray-800 focus:ring-2 focus:ring-gray-800 focus:ring-offset-2 transition duration-200 font-medium text-lg"
                >
                    Sign In
                </button>
            </form>

            <!-- Register Link -->
            <p class="mt-8 text-center text-sm text-gray-600">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-gray-800 hover:text-gray-600 font-medium ml-1">
                    Register here
                </a>
            </p>

            <!-- Divider -->
            <div class="mt-8">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-gray-50 text-gray-500">Or continue with</span>
                    </div>
                </div>
            </div>

            <!-- Social Login Options -->
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
    // Handle form submission and error display
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const errorDiv = document.getElementById('error-message');
        
        // Show error message if there are Laravel errors
        @if ($errors->any())
            errorDiv.textContent = "{{ $errors->first() }}";
            errorDiv.classList.remove('hidden');
        @endif
        
        // Add form validation
        form.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            if (!email || !password) {
                e.preventDefault();
                errorDiv.textContent = 'Please fill in all required fields.';
                errorDiv.classList.remove('hidden');
                return;
            }
            
            if (!isValidEmail(email)) {
                e.preventDefault();
                errorDiv.textContent = 'Please enter a valid email address.';
                errorDiv.classList.remove('hidden');
                return;
            }
        });
        
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
    });
</script>

</body>
</html>