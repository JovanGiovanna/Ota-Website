@extends('layouts.superadmin')

@section('title', 'Email Notification Settings')

@section('welcome')
Email Notification Settings
@endsection

@section('logout_route', route('super_admin.logout'))

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="bg-white shadow overflow-hidden sm:rounded-md mb-6">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Booking Notification Settings</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Manage email addresses to receive booking notifications</p>
        </div>

        {{-- Add New Email Form --}}
        <div class="px-4 py-5 sm:p-6 bg-gray-50">
            <form action="{{ route('super_admin.email_settings.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label for="label" class="block text-sm font-medium text-gray-700">Label</label>
                        <input type="text" name="label" id="label" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="e.g., Head Office">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" name="email" id="email" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="headbase@example.com">
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <input type="text" name="description" id="description"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="Optional description">
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" checked
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                            Active (send notifications to this email)
                        </label>
                    </div>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add Email
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Email List --}}
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Email Recipients</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Label
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email Address
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Description
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($emailSettings as $setting)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $setting->label }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $setting->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500">{{ $setting->description ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form action="{{ route('super_admin.email_settings.toggle', $setting->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="relative inline-flex items-center">
                                        @if($setting->is_active)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Inactive
                                            </span>
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button onclick="openEditModal({{ $setting->id }}, '{{ $setting->label }}', '{{ $setting->email }}', '{{ $setting->description }}', {{ $setting->is_active ? 'true' : 'false' }})"
                                    class="text-blue-600 hover:text-blue-900 mr-3">
                                    Edit
                                </button>
                                <form action="{{ route('super_admin.email_settings.destroy', $setting->id) }}" method="POST" class="inline" onsubmit="return confirmDeleteAction(this);" data-delete-item="{{ $setting->label }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                No email recipients configured yet. Add one above to start receiving booking notifications.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div id="editModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Edit Email Notification</h3>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Label</label>
                        <input type="text" name="label" id="edit_label" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" name="email" id="edit_email" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <input type="text" name="description" id="edit_description"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="edit_is_active"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="edit_is_active" class="ml-2 block text-sm text-gray-900">
                            Active
                        </label>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-5">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openEditModal(id, label, email, description, isActive) {
        document.getElementById('editForm').action = `/super-admin/email-settings/${id}`;
        document.getElementById('edit_label').value = label;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_description').value = description || '';
        document.getElementById('edit_is_active').checked = isActive;
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });
</script>
@endpush
@endsection
