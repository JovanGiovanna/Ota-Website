@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">User Registration</h1>

    <form id="registerForm" class="bg-white rounded-lg shadow p-6 space-y-4 max-w-md mx-auto">
        @csrf
        
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
            <input type="text" id="name" name="name" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500" placeholder="John Doe" />
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
            <input type="email" id="email" name="email" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500" placeholder="john@example.com" />
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" id="password" name="password" required minlength="6" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter password" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Confirm password" />
        </div>

        <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white font-medium rounded-md hover:bg-blue-600 transition">
            Create Account
        </button>

        <p class="text-center text-sm text-gray-600 pt-2">
            Already have an account? <a href="{{ route('login') }}" class="text-blue-500 hover:text-blue-700 font-medium">Login here</a>
        </p>
    </form>
</div>

@include('components.sweetalert')

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // Validate passwords match
        const password = document.getElementById('password').value;
        const confirmation = document.getElementById('password_confirmation').value;
        
        if (password !== confirmation) {
            showWarning('Password Mismatch', 'Passwords do not match. Please try again.');
            return;
        }
        
        showLoading('Creating Account...', 'Please wait while we create your account');
        
        try {
            const formData = new FormData(form);
            const response = await fetch('{{ route('register.web') }}', {
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
                }, data.delay || 2000);
            }
        } catch (error) {
            Swal.close();
            showError('Error', error.message);
        }
    });
});
</script>
@endsection
