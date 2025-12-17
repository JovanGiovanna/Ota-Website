@extends('layouts.user')

@section('title', 'Activate Account')

@section('welcome')
Activate Your Account
@endsection

@section('content')
<!-- Activation Hero -->
<div class="bg-gradient-to-r from-purple-600 via-pink-600 to-red-600 rounded-2xl p-8 mb-8 text-white shadow-2xl">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold mb-2">Activate Your Account</h1>
            <p class="text-purple-100">Set up your password to complete account activation</p>
        </div>
        <div class="hidden md:block">
            <div class="w-20 h-20 bg-white/10 rounded-full flex items-center justify-center">
                <i class="fas fa-key text-3xl text-white"></i>
            </div>
        </div>
    </div>
</div>

<!-- Activation Form -->
<div class="max-w-md mx-auto bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
    <div class="text-center mb-6">
        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-user-plus text-purple-600 text-2xl"></i>
        </div>
        <h2 class="text-xl font-bold text-gray-800">Welcome, {{ $user->name }}!</h2>
        <p class="text-gray-600">Please set up your password to activate your account</p>
    </div>

    <form method="POST" action="{{ route('user.account.set_password', $token) }}">
        @csrf

        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-gray-400"></i>
                </div>
                <input type="email" value="{{ $user->email }}" readonly class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 bg-gray-50">
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>
                <input type="password" name="password" required class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
            <p class="text-xs text-gray-500 mt-1">Password must be at least 8 characters long</p>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>
                <input type="password" name="password_confirmation" required class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
        </div>

        <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-xl font-bold text-lg hover:from-purple-700 hover:to-pink-700 transition-all duration-200 shadow-lg transform hover:scale-105">
            <i class="fas fa-key mr-2"></i>
            Activate Account
        </button>
    </form>

    <div class="text-center mt-6">
        <p class="text-gray-600 text-sm">
            Already have an account?
            <a href="{{ route('login') }}" class="text-purple-600 hover:text-purple-700 font-semibold">Sign in here</a>
        </p>
    </div>
</div>
@endsection
