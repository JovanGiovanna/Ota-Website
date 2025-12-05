@extends('layouts.admin')

@section('title', 'Register Admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl mx-auto bg-white p-8 rounded-lg shadow">
        <h1 class="text-2xl font-bold mb-4">Register Admin</h1>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 p-3 rounded mb-4">
                <ul class="text-sm text-red-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.register.web') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-3 py-2 border rounded" />
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-3 py-2 border rounded" />
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" required class="w-full px-3 py-2 border rounded" />
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700">Role</label>
                <select name="role" class="w-full px-3 py-2 border rounded">
                    @foreach(\App\Models\Admin::ROLES as $r)
                        <option value="{{ $r }}" {{ old('role') == $r ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $r)) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Register</button>
            </div>
        </form>
    </div>
</div>
@endsection
