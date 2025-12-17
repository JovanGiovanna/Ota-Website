@extends('layouts.superadmin')

@section('title', 'Manage Roles')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Role Management</h1>
        <a href="{{ route('super_admin.roles.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Create Role</a>
    </div>

    @if(session('success'))<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">{{ session('error') }}</div>@endif

    <div class="bg-white rounded shadow overflow-hidden">
        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Key</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permissions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Used By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($roles as $role)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $role->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $role->key }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate" title="{{ $role->permissions->pluck('name')->join(', ') }}">
                        {{ $role->permissions->pluck('name')->join(', ') ?? 'No permissions' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $role->admins()->count() }} admin(s)
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('super_admin.roles.edit', $role) }}" class="text-blue-600 hover:underline mr-3">Edit</a>
                        <form action="{{ route('super_admin.roles.destroy', $role) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this role?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 {{ $role->admins()->exists() ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $role->admins()->exists() ? 'disabled' : '' }}>Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        <div class="p-4">{{ $roles->links() }}</div>
    </div>

    <div class="mt-6">
        <a href="{{ route('super_admin.admins') }}" class="text-blue-600 hover:underline">← Back to Admin Management</a>
    </div>
</div>
@endsection
