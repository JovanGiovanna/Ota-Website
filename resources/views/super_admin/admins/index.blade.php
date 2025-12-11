@extends('layouts.superadmin')

@section('title', 'Manage Admins')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Admin Management</h1>
        <a href="{{ route('super_admin.admins.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Create Admin</a>
    </div>

    @if(session('success'))<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">{{ session('error') }}</div>@endif

    <div class="bg-white rounded shadow overflow-hidden">
        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permissions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($admins as $admin)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $admin->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $admin->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $admin->role }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate" title="{{ $admin->permissions->pluck('name')->join(', ') }}">
                        {{ $admin->permissions->pluck('name')->join(', ') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('super_admin.admins.edit', $admin) }}" class="text-blue-600 hover:underline mr-3">Edit</a>
                        <form action="{{ route('super_admin.admins.destroy', $admin) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this admin?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        <div class="p-4">{{ $admins->links() }}</div>
    </div>
</div>
@endsection
