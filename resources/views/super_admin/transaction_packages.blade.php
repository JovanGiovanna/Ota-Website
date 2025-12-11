@extends('layouts.superadmin')

@section('title', 'Transaction Packages Management')

@section('welcome')
Transaction Packages Management
@endsection

@section('logout_route', route('super_admin.logout'))

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Transaction Packages</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Manage all package transactions</p>
            </div>
            <div class="flex space-x-3">
                <button class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
            <div class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-0">
                    <label for="search" class="sr-only">Search transactions</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="search" name="search" id="search" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Search transactions...">
                    </div>
                </div>
                <div>
                    <select id="status" name="status" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="">All Status</option>
                        <option value="completed">Completed</option>
                        <option value="pending">Pending</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="refunded">Refunded</option>
                    </select>
                </div>
                <div>
                    <select id="date_range" name="date_range" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="">All Dates</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="overflow-x-auto">
            <table class="min-w-[1200px] w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Transaction ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Customer</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Packages</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Quantity</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Amount</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Date</th>
                        <th scope="col" class="relative px-6 py-3 whitespace-nowrap">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $transaction->booking_code ?? '#' . strtoupper(substr($transaction->id, 0, 8)) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8">
                                    <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                                        <span class="text-white text-xs font-medium">{{ substr($transaction->user->name ?? 'N/A', 0, 2) }}</span>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $transaction->user->name ?? $transaction->booker_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $transaction->user->email ?? $transaction->booker_email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                {{ $transaction->packages ? $transaction->packages->pluck('name_package')->join(', ') : 'No packages' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $transaction->packages ? $transaction->packages->sum('quantity') : 0 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="text-sm font-medium text-gray-900">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($transaction->status == 'completed')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                            @elseif($transaction->status == 'pending')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            @elseif($transaction->status == 'cancelled')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Cancelled</span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($transaction->status) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $transaction->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button
                                type="button"
                                class="text-blue-600 hover:text-blue-900 mr-3"
                                data-modal-open
                                data-booking-code="{{ $transaction->booking_code ?? 'N/A' }}"
                                data-customer-name="{{ $transaction->user->name ?? $transaction->booker_name ?? 'N/A' }}"
                                data-customer-email="{{ $transaction->user->email ?? $transaction->booker_email ?? 'N/A' }}"
                                data-packages="{{ $transaction->packages ? $transaction->packages->pluck('name_package')->join(', ') : 'N/A' }}"
                                data-quantity="{{ $transaction->packages ? $transaction->packages->sum('quantity') : 0 }}"
                                data-total-price="{{ number_format($transaction->total_price, 0, ',', '.') }}"
                                data-status="{{ ucfirst($transaction->status) }}"
                                data-date="{{ $transaction->created_at->format('M d, Y H:i') }}"
                                data-checkin="{{ optional($transaction->checkin_appointment_start)->format('M d, Y H:i') ?? 'N/A' }}"
                                data-checkout="{{ optional($transaction->checkout_appointment_end)->format('M d, Y H:i') ?? 'N/A' }}"
                                data-packages-detail="{{ $transaction->packages ? json_encode($transaction->packages->map(function($p){ return ['name' => $p->name_package, 'qty' => $p->quantity ?? 1, 'products' => $p->products ? $p->products->pluck('name')->join(', ') : 'N/A']; })) : '[]' }}"
                                data-addons-detail="{{ $transaction->addons ? json_encode($transaction->addons->map(function($a){ return ['name' => $a->addons, 'qty' => $a->pivot->quantity ?? 1, 'price' => $a->price]; })) : '[]' }}"
                            >
                                View
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            No transactions found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            {{ $transactions->links() }}
        </div>

        <!-- Detail Modal -->
        <div id="transactionModal" class="fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50 hidden">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl mx-4 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <div>
                        <p class="text-xs text-gray-500">Booking Code</p>
                        <h3 id="modalBookingCode" class="text-lg font-semibold text-gray-800">-</h3>
                    </div>
                    <button type="button" id="modalClose" class="text-gray-500 hover:text-gray-700 text-2xl leading-none">×</button>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-800">
                    <div class="space-y-1">
                        <p class="text-xs text-gray-500">Customer</p>
                        <p class="font-semibold" id="modalCustomerName">-</p>
                        <p class="text-gray-600" id="modalCustomerEmail">-</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-gray-500">Status</p>
                        <p class="font-semibold" id="modalStatus">-</p>
                        <p class="text-xs text-gray-500 mt-2">Date</p>
                        <p class="text-gray-700" id="modalDate">-</p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-xs text-gray-500">Quantity</p>
                        <p class="font-semibold" id="modalQuantity">-</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-gray-500">Total Price</p>
                        <p class="font-semibold text-blue-700" id="modalTotalPrice">-</p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-xs text-gray-500">Check-in</p>
                        <p class="text-gray-700" id="modalCheckin">-</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-gray-500">Check-out</p>
                        <p class="text-gray-700" id="modalCheckout">-</p>
                    </div>

                    <!-- Packages Detail -->
                    <div class="md:col-span-2 mt-4">
                        <h4 class="font-semibold text-gray-800 mb-3">📦 Packages</h4>
                        <div id="modalPackagesDetail" class="space-y-3">
                            <!-- Populated by JS -->
                        </div>
                    </div>

                    <!-- Addons Detail -->
                    <div class="md:col-span-2 mt-4">
                        <h4 class="font-semibold text-gray-800 mb-3">➕ Add-ons</h4>
                        <div id="modalAddonsDetail" class="space-y-2">
                            <!-- Populated by JS -->
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                    <button type="button" id="modalCloseBottom" class="px-4 py-2 rounded bg-gray-100 text-gray-700 hover:bg-gray-200">Close</button>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modal = document.getElementById('transactionModal');
                const closeButtons = [document.getElementById('modalClose'), document.getElementById('modalCloseBottom')];

                const fields = {
                    bookingCode: document.getElementById('modalBookingCode'),
                    customerName: document.getElementById('modalCustomerName'),
                    customerEmail: document.getElementById('modalCustomerEmail'),
                    quantity: document.getElementById('modalQuantity'),
                    totalPrice: document.getElementById('modalTotalPrice'),
                    status: document.getElementById('modalStatus'),
                    date: document.getElementById('modalDate'),
                    checkin: document.getElementById('modalCheckin'),
                    checkout: document.getElementById('modalCheckout'),
                    packagesDetail: document.getElementById('modalPackagesDetail'),
                    addonsDetail: document.getElementById('modalAddonsDetail'),
                };

                function openModal(event) {
                    const btn = event.currentTarget;
                    fields.bookingCode.textContent = btn.dataset.bookingCode || '-';
                    fields.customerName.textContent = btn.dataset.customerName || '-';
                    fields.customerEmail.textContent = btn.dataset.customerEmail || '-';
                    fields.quantity.textContent = btn.dataset.quantity || '-';
                    fields.totalPrice.textContent = btn.dataset.totalPrice ? 'Rp ' + btn.dataset.totalPrice : '-';
                    fields.status.textContent = btn.dataset.status || '-';
                    fields.date.textContent = btn.dataset.date || '-';
                    fields.checkin.textContent = btn.dataset.checkin || '-';
                    fields.checkout.textContent = btn.dataset.checkout || '-';

                    // Parse and display packages detail
                    try {
                        const packages = JSON.parse(btn.dataset.packagesDetail || '[]');
                        if (packages.length > 0) {
                            fields.packagesDetail.innerHTML = packages.map(pkg => `
                                <div class="bg-blue-50 border-l-4 border-blue-500 p-3 rounded">
                                    <p class="font-semibold text-gray-800">${pkg.name} <span class="text-sm text-gray-600">(Qty: ${pkg.qty})</span></p>
                                    <p class="text-sm text-gray-600 mt-1">Products: ${pkg.products || 'N/A'}</p>
                                </div>
                            `).join('');
                        } else {
                            fields.packagesDetail.innerHTML = '<p class="text-gray-500">No packages</p>';
                        }
                    } catch (e) {
                        fields.packagesDetail.innerHTML = '<p class="text-gray-500">No packages</p>';
                    }

                    // Parse and display addons detail
                    try {
                        const addons = JSON.parse(btn.dataset.addonsDetail || '[]');
                        if (addons.length > 0) {
                            fields.addonsDetail.innerHTML = addons.map(addon => `
                                <div class="bg-green-50 border-l-4 border-green-500 p-3 rounded">
                                    <p class="font-semibold text-gray-800">${addon.name} <span class="text-sm text-gray-600">(Qty: ${addon.qty})</span></p>
                                    <p class="text-sm text-gray-600">Price: Rp ${new Intl.NumberFormat('id-ID').format(addon.price)}</p>
                                </div>
                            `).join('');
                        } else {
                            fields.addonsDetail.innerHTML = '<p class="text-gray-500">No add-ons</p>';
                        }
                    } catch (e) {
                        fields.addonsDetail.innerHTML = '<p class="text-gray-500">No add-ons</p>';
                    }

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }

                function closeModal() {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }

                document.querySelectorAll('[data-modal-open]').forEach(btn => btn.addEventListener('click', openModal));
                closeButtons.forEach(btn => btn && btn.addEventListener('click', closeModal));
                modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
            });
        </script>
    </div>
</div>
@endsection
