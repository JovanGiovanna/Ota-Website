@extends('layouts.superadmin')

@section('title', $role ? 'Edit Role' : 'Create Role')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-2xl font-bold mb-4">{{ $role ? 'Edit Role' : 'Create Role' }}</h1>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $role ? route('super_admin.roles.update', $role) : route('super_admin.roles.store') }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf
        @if($role)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Role Name</label>
                <input type="text" name="name" value="{{ old('name', $role->name ?? '') }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required placeholder="e.g., Admin, Manager, Editor">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Role Key</label>
                <input type="text" name="key" value="{{ old('key', $role->key ?? '') }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required placeholder="e.g., admin, manager, editor">
                <p class="text-xs text-gray-500 mt-1">Use lowercase letters and underscores only</p>
            </div>
        </div>

        <div class="mt-6">
            <h3 class="font-semibold mb-3">Assign Permissions</h3>
            <div class="border border-gray-200 rounded-md p-4 max-h-96 overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($permissions as $perm)
                        <label class="inline-flex items-center space-x-2 p-2 hover:bg-gray-50 rounded">
                            <input type="checkbox" name="permissions[]" value="{{ $perm->id }}" 
                                @if(old('permissions') && in_array($perm->id, old('permissions'))) checked
                                @elseif(!old('permissions') && $role && $role->permissions->pluck('id')->contains($perm->id)) checked
                                @endif
                                class="rounded border-gray-300">
                            <span class="text-sm">
                                <span class="font-medium">{{ $perm->name }}</span>
                                <span class="text-gray-500">({{ $perm->key }})</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Total: {{ $permissions->count() }} permissions available</p>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">{{ $role ? 'Update Role' : 'Create Role' }}</button>
            <a href="{{ route('super_admin.roles') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Cancel</a>
        </div>
    </form>
</div>
@endsection
