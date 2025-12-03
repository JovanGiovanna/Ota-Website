@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">User Login</h1>

    <form id="loginForm" class="bg-white rounded-lg shadow p-6 space-y-4 max-w-md mx-auto">
        @csrf
        
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
            <input type="email" id="email" name="email" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500" placeholder="john@example.com" />
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" id="password" name="password" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter password" />
        </div>

        <div class="flex items-center">
            <input type="checkbox" id="remember" name="remember" class="h-4 w-4 text-blue-600" />
            <label for="remember" class="ml-2 text-sm text-gray-600">Remember me</label>
        </div>

        <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white font-medium rounded-md hover:bg-blue-600 transition">
            Login
        </button>

        <p class="text-center text-sm text-gray-600 pt-2">
            Don't have an account? <a href="{{ route('register') }}" class="text-blue-500 hover:text-blue-700 font-medium">Register here</a>
        </p>
    </form>
</div>

@include('components.sweetalert')

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        showLoading('Logging In...', 'Please wait while we verify your credentials');
        
        try {
            const formData = new FormData(form);
            const response = await fetch('{{ route('login.web') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            });

            const data = await response.json();
            Swal.close();
            showNotification(data);
            
            if (response.ok && data.redirect) {
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, data.delay || 1500);
            }
        } catch (error) {
            Swal.close();
            showError('Error', error.message);
        }
    });
});
</script>
@endsection
