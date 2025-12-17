@extends('layouts.superadmin')

@section('title', 'Manage Admins')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Admin Management</h1>
        <div class="flex gap-3">
            <a href="{{ route('super_admin.roles') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Manage Roles</a>
            <a href="{{ route('super_admin.admins.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Create Admin</a>
        </div>
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
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($admins as $admin)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $admin->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $admin->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $admin->role }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate" title="{{ $admin->roles->flatMap(function($role) { return $role->permissions->pluck('name'); })->unique()->join(', ') }}">
                        {{ $admin->roles->flatMap(function($role) { return $role->permissions->pluck('name'); })->unique()->join(', ') ?: 'No permissions' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <button type="button" onclick="showAdminDetail({{ json_encode(['name' => $admin->name, 'email' => $admin->email, 'role' => $admin->role, 'roles' => $admin->roles->pluck('name'), 'permissions' => $admin->roles->flatMap(function($role) { return $role->permissions->pluck('name'); })->unique()->values()]) }})" class="text-green-600 hover:underline mr-3">View</button>
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

<!-- Admin Detail Modal -->
<div id="adminDetailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-96 overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-bold text-white">Admin Detail</h2>
            <button type="button" onclick="closeAdminDetail()" class="text-white hover:text-gray-200 text-2xl">&times;</button>
        </div>
        <div id="detailContent" class="p-6">
            <!-- Detail akan ditampilkan di sini -->
        </div>
    </div>
</div>

<script>
    function showAdminDetail(admin) {
        const rolesHtml = admin.roles.length > 0 
            ? admin.roles.map(role => `<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 mr-2">${role}</span>`).join('')
            : '<span class="text-gray-500">No roles assigned</span>';
        
        const permissionsHtml = admin.permissions.length > 0
            ? admin.permissions.map(perm => `<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 mr-2 mb-2">${perm}</span>`).join('')
            : '<span class="text-gray-500">No permissions</span>';

        const html = `
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-gray-600 text-sm font-medium">Name</span>
                        <p class="text-lg font-bold text-gray-900">${admin.name}</p>
                    </div>
                    <div>
                        <span class="text-gray-600 text-sm font-medium">Email</span>
                        <p class="text-lg font-bold text-gray-900">${admin.email}</p>
                    </div>
                </div>
                <div class="border-t pt-4">
                    <span class="text-gray-600 text-sm font-medium">Legacy Role</span>
                    <p class="text-lg font-bold text-gray-900">${admin.role}</p>
                </div>
                <div class="border-t pt-4">
                    <span class="text-gray-600 text-sm font-medium block mb-2">Assigned Roles</span>
                    <div class="flex flex-wrap">
                        ${rolesHtml}
                    </div>
                </div>
                <div class="border-t pt-4">
                    <span class="text-gray-600 text-sm font-medium block mb-2">Permissions</span>
                    <div class="flex flex-wrap">
                        ${permissionsHtml}
                    </div>
                </div>
            </div>
        `;
        document.getElementById('detailContent').innerHTML = html;
        document.getElementById('adminDetailModal').classList.remove('hidden');
    }

    function closeAdminDetail() {
        document.getElementById('adminDetailModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('adminDetailModal').addEventListener('click', function(e) {
        if (e.target === this) closeAdminDetail();
    });
</script>
@endsection
