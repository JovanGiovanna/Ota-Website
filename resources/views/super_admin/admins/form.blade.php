@extends('layouts.superadmin')

@section('title', $admin ? 'Edit Admin' : 'Create Admin')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-2xl font-bold mb-4">{{ $admin ? 'Edit Admin' : 'Create Admin' }}</h1>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $admin ? route('super_admin.admins.update', $admin) : route('super_admin.admins.store') }}" method="POST">
        @csrf
        @if($admin)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" value="{{ old('name', $admin->name ?? '') }}" class="mt-1 block w-full border-gray-300 rounded-md p-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email', $admin->email ?? '') }}" class="mt-1 block w-full border-gray-300 rounded-md p-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Roles</label>
                <select name="roles[]" class="mt-1 block w-full border-gray-300 rounded-md p-2">
                    <option value="" disabled @selected(empty(old('roles', $admin?->roles->pluck('id')->first())))>Pilih salah satu role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" @selected((old('roles') && in_array($role->id, old('roles'))) || (!old('roles') && $admin?->roles->pluck('id')->contains($role->id)))>
                            {{ $role->name }} ({{ $role->key }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Password @if(!$admin) @endif</label>
                <input type="password" name="password" class="mt-1 block w-full border-gray-300 rounded-md p-2">
                <input type="password" name="password_confirmation" class="mt-2 block w-full border-gray-300 rounded-md p-2" placeholder="Confirm password">
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">{{ $admin ? 'Update Admin' : 'Create Admin' }}</button>
            <a href="{{ route('super_admin.admins') }}" class="ml-3 text-gray-600">Cancel</a>
        </div>
    </form>
</div>
@endsection
